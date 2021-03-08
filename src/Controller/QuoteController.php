<?php

namespace App\Controller;

use http\Exception;
use App\Entity\Quote;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Discount;
use App\Entity\ProductQuote;
use App\Repository\SaleRepository;
use App\Repository\QuoteRepository;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PaymentMethodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quote")
 */
class QuoteController extends AbstractController
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
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    private $security;
    /**
     * @var ClientRepository
     */
    private $clientRepository;


    public function __construct(ClientRepository $clientRepository, QuoteRepository $quoteRepository, DiscountRepository $discountRepository, CategoryRepository $categoryRepository, SaleRepository $saleRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager,PaymentMethodRepository $paymentMethodRepository, Security $security){
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->saleRepository = $saleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->discountRepository = $discountRepository;
        $this->quoteRepository = $quoteRepository;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/", name="quote", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {

        $user = $this->security->getUser();

        return $this->render('quote/index.html.twig', [
            'products' => $productRepository->findOneByCompany($user->getCompany()),
            'paymentMethods' => $this->paymentMethodRepository->findAll(),
            'categories' => $this->categoryRepository->findByCompany($user->getCompany())
        ]);

    }

    /**
     * @Route("/{id}/reciept", name="quote_reciept", methods={"GET"})
     * @param QuoteRepository $quoteRepository
     * @param $id
     * @return Response
     */
    public function reciept(QuoteRepository $quoteRepository,$id): Response
    {

        $user = $this->security->getUser();
        $quote = $this->quoteRepository->findOneBy(['id'=>$id]);

        if ($this->container->has('profiler'))
        {
            $this->container->get('profiler')->disable();
        }
        return $this->render('reciept/reciept_quote.html.twig', [
            'quote' => $quote,
            'products' => $quote->getProductQuotes(),
            'discounts'=>$quote->getDiscounts()
        ]);

    }

    /**
     * @Route("/create", name="create_quote", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function create(Request $request):JsonResponse
    {
        $totalDiscount = 0;
        if ($request->getMethod() == 'POST')
        {
            $total = $request->request->get('total');
            $subtotal = $request->request->get('subtotal');
            $tax = $request->request->get('tax');
            $products = $request->request->get('products');
            $quantity = $request->request->get('quantity');
            $clientData = $request->request->get('client');
            $price = $request->request->get('price');
            $productName = $request->request->get('name');
            $discounts = $request->request->get('discountId');
            $reason = $request->request->get('reason');
            $discountAmount = $request->request->get('discount');
            $SKUs = $request->request->get('sku');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $quote = new Quote();

        $quote->setTotal($total);
        $quote->setSubtotal($subtotal);
        $quote->setTax($tax);


        if($clientData == ""){
            $client = $this->clientRepository->searchOneByName('Publico En General',$this->security->getUser()->getCompany());
            $quote->setClient($client);
        }else{
            $client = $this->clientRepository->searchOneByName($clientData,$this->security->getUser()->getCompany());

            if($client == null){
                $client = new Client();
                $client->setName($clientData);
                $em->persist($client);
                $em->flush();
                $quote->setClient($client);
            }else{
                $quote->setClient($client);
            }
        }
        
        $time = new \DateTime();
        $expiration = new \DateTime();

        $quote->setTime($time);
        $quote->setExpirationdate($expiration->modify('+1 month'));

        $quote->setUser($this->security->getUser());
        $quote->setCompany($this->security->getUser()->getCompany());

        $company = $this->security->getUser()->getCompany();
        $em->persist($quote);
        $em->flush();

        $count = 0;

        foreach ($products as $prod ){

            if($prod == -1){
                $tempProduct = new Product();
                $tempProduct->setName($productName[$count]);
                $tempProduct->setPrice($price[$count]/$quantity[$count]);
                $tempProduct->setSKU($SKUs[$count]);

                $tempProduct->setCompany($this->security->getUser()->getCompany());
                $tempProduct->setIsTaxable(true);

                $em->persist($tempProduct);
                $em->flush();
                $prod = $tempProduct->getId();
            }

            $product = $this->productRepository->findOneBy(['id'=>$prod]);
            $productQuote = new ProductQuote();

            $productQuote->setProduct($product);
            $productQuote->setAmount($quantity[$count]);
            $productQuote->setQuote($quote);
            $productQuote->setCompany($company);


            if($product->getPrice()==0){
                $productQuote->setPrice($price[$count]);
            }else{
                if($product->getPrice()*$quantity[$count]== $price[$count]){
                    $productQuote->setPrice($price[$count]);
                }else{
                    $productQuote->setPrice($product->getPrice()*$quantity[$count]);
                    $productQuote->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);
                    $productQuote->setCompany($company);

                    $discountCount = 0;

                    foreach ($discounts as $discount){

                        if($discount == $prod){
                            $productDiscount = new Discount();
                            $productDiscount->setProductQuote($productQuote);
                            $productDiscount->setName($reason[$discountCount]);
                            $productDiscount->setAmount($discountAmount[$discountCount]);
                            $productDiscount->setCompany($company);

                            $productDiscount->setQuote($quote);
                            $totalDiscount = $totalDiscount + $discountAmount[$discountCount];
                            $em->persist($productDiscount);
                        }
                        $discountCount++;

                    }
                    $quote->setDiscount($totalDiscount);
                }
                $quote->setDiscount($totalDiscount);

            }

            $em->persist($quote);
            $em->persist($productQuote);


            $count++;
        }
        $em->flush();


        $response = $quote->getId();

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    




}
