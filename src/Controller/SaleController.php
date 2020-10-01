<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Entity\Patient;
use App\Entity\Payment;
use App\Entity\PaymentMethod;
use App\Entity\ProductSold;
use App\Entity\Sale;
use App\Repository\CategoryRepository;
use App\Repository\DiscountRepository;
use App\Repository\PatientRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/sale")
 */
class SaleController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var PaymentMethodRepository
     */
    private $paymentMethodRepository;

    /**
     * @var SaleRepository
     */
    private $saleRepository;

    /**
     * @var DiscountRepository
     */
    private $discountRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    private $security;





    public function __construct(DiscountRepository $discountRepository, CategoryRepository $categoryRepository, SaleRepository $saleRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager,PaymentMethodRepository $paymentMethodRepository, Security $security){
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->saleRepository = $saleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->discountRepository = $discountRepository;
    }

    /**
     * @Route("/", name="sale", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {

        $user = $this->security->getUser();

        return $this->render('sale/index.html.twig', [
            'products' => $productRepository->findAll(),
            'paymentMethods' => $this->paymentMethodRepository->findAll(),
            'categories' => $this->categoryRepository->findAll()
        ]);

    }

    /**
     * @Route("/{id}/reciept", name="reciept", methods={"GET"})
     * @param SaleRepository $saleRepository
     * @param $id
     * @return Response
     */
    public function reciept(SaleRepository $saleRepository,$id): Response
    {

        $user = $this->security->getUser();
        $sale = $this->saleRepository->findOneBy(['id'=>$id]);
        //$discount = $this->discountRepository->findBy(['sale'=>$id]);

        if ($this->container->has('profiler'))
        {
            $this->container->get('profiler')->disable();
        }
        return $this->render('reciept/reciept.html.twig', [
            'sale' => $sale,
            'products' => $sale->getProducts(),
            'payments'=>$sale->getPayments(),
            'discounts'=>$sale->getDiscounts()
        ]);

    }

    /**
     * @Route("/{id}/payment", name="sale_payment_new", methods={"GET","POST"})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function payment($id,Request $request): Response
    {

        $user = $this->security->getUser();
        $sale = $this->saleRepository->findOneBy(['id'=>$id]);

        return $this->render('sale/payment.html.twig', [
            'paymentMethods' => $this->paymentMethodRepository->findAll(),
            'sale' => $sale
        ]);

    }


    /**
     * @Route("/create", name="create_sale", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $total = $request->request->get('total');
            $subtotal = $request->request->get('subtotal');
            $tax = $request->request->get('tax');
            $products = $request->request->get('products');
            $quantity = $request->request->get('quantity');
            $client = $request->request->get('client');
            $price = $request->request->get('price');

            $discounts = $request->request->get('discountId');
            $reason = $request->request->get('reason');
            $discountAmount = $request->request->get('discount');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = new Sale();

        $sale->setTotal($total);
        $sale->setSubtotal($subtotal);
        $sale->setTax($tax);

        if($client == ""){
            $sale->setClient("Publico en General");
        }else{
            $sale->setClient($client);
        }

        $sale->setTime(new \DateTime());



        $em->persist($sale);
        $em->flush();

        $count = 0;

        foreach ($products as $prod ){
            $product = $this->productRepository->findOneBy(['id'=>$prod]);
            $productSold = new ProductSold();

            if($product->getQuantity() != null){
                $product->setQuantity($product->getQuantity()-$quantity[$count]);
            }

            $productSold->setProduct($product);
            $productSold->setAmount($quantity[$count]);
            $productSold->setSale($sale);

            if($product->getPrice()==0){
                $productSold->setPrice($price[$count]);
            }else{
            if($product->getPrice()/$quantity[$count]==$price[$count]){
                $productSold->setPrice($price[$count]);
            }else{
                $productSold->setPrice($product->getPrice()*$quantity[$count]);
                $productSold->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);

                $discountCount = 0;
                foreach ($discounts as $discount){

                    if($discount == $prod){
                        $productDiscount = new Discount();
                        $productDiscount->setProductSold($productSold);
                        $productDiscount->setName($reason[$discountCount]);
                        $productDiscount->setAmount($discountAmount[$discountCount]);
                        $productDiscount->setSale($sale);
                        $em->persist($productDiscount);
                    }
                    $discountCount++;

                }

                }
            }
            $em->persist($productSold);

            $em->persist($product);

            $count++;
        }
        $em->flush();


        $response = $sale->getId();

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/createpayment", name="create_sale_payment", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createPayment(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $payments = $request->request->get('payments');
            $amounts = $request->request->get('amounts');
            $saleID = $request->request->get('saleID');
            $commission = $request->request->get('commission');
        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleID]);

        $count = 0;

        foreach ($payments as $pay){
            $paymentMethod = $this->paymentMethodRepository->findOneBy(['id'=>$pay]);
            $payment = new Payment();

            $payment->setType($paymentMethod);
            $payment->setAmount($amounts[$count]);
            $payment->setSale($sale);

            $em->persist($payment);


            $em->flush();

            $count++;
        }

        $sale->setCommission($commission);
        $em->persist($sale);
        $em->flush();


        $response = "1";

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchproduct", name="fetch_product", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchProduct(Request $request, ProductRepository $repository):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('id');
        }
        else {
            die();
        }

        $results = $this->productRepository->findOneBy(['id'=>$id]);

        $response = '{"id":"'.$results->getId().'","name":"'.$results->getName().'","tax":"'.$results->getIsTaxable().'","price":"'.$results->getPrice().'"}';


        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }



}
