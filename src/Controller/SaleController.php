<?php

namespace App\Controller;

use Exception;
use App\Entity\Sale;
use App\Entity\Patient;
use App\Entity\Payment;
use App\Entity\Discount;
use App\Entity\ProductSold;
use App\Entity\ProductStock;
use App\Entity\PaymentMethod;
use App\Repository\SaleRepository;
use App\Repository\UserRepository;
use App\Entity\ProductSoldDiscount;
use Doctrine\ORM\NoResultException;
use App\Repository\ClientRepository;
use App\Repository\PatientRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductSoldRepository;
use Doctrine\ORM\NonUniqueResultException;
use App\Repository\PaymentMethodRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductSoldDiscountRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    /**
     * @var ProductSoldRepository
     */
    private $productSoldRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;
    
     /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ProductSoldDiscountRepository
     */
    private $productSoldDiscountRepository;

    


    private $session;


    public function __construct(ProductSoldDiscountRepository $productSoldDiscountRepository, ClientRepository $clientRepository, UserRepository $userRepository, ProductSoldRepository $productSoldRepository, DiscountRepository $discountRepository, CategoryRepository $categoryRepository, SaleRepository $saleRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager,PaymentMethodRepository $paymentMethodRepository, Security $security,SessionInterface $session){
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->saleRepository = $saleRepository;
        $this->categoryRepository = $categoryRepository;
        $this->discountRepository = $discountRepository;
        $this->productSoldRepository = $productSoldRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->productSoldDiscountRepository = $productSoldDiscountRepository;
        $this->session = $session;

    }



    /**
     * @Route("/", name="sale", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {

        $user = $this->security->getUser();
       // $counts = $this->saleRepository->GetMostSoldProducts();

        return $this->render('sale/index.html.twig', [
            'products' => $productRepository->findByCompany($user->getCompany()),
            'paymentMethods' => $this->paymentMethodRepository->findByCompany($user->getCompany()),
            'categories' => $this->categoryRepository->findByCompany($user->getCompany()),
            'discounts' =>  $this->discountRepository->findByCompany($user->getCompany()),
            'sales' => $this->saleRepository->findRecentByCompany($user->getCompany())

        ]);

    }

    /**
     * @Route("/{id}", name="unpaid_sale_edit", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function EditSale(ProductRepository $productRepository,$id): Response
    {

        $user = $this->security->getUser();
        $sale = $this->saleRepository->findOneBy(['id'=>$id]);
        
        return $this->render('sale/edit_sale2.html.twig', [
            'products' => $productRepository->findByCompany($user->getCompany()),
            'sale' => $sale,
            'productsold' => $sale->getProducts(),
            'payments'=>$sale->getPayments(),
            'discounts'=>$sale->getProductSoldDiscounts(),
            'baseDiscounts' =>  $this->discountRepository->findByCompany($user->getCompany()),
            'paymentMethods' => $this->paymentMethodRepository->findByCompany($user->getCompany()),
            'categories' => $this->categoryRepository->findByCompany($user->getCompany())
        ]);

    }

    /**
     * @Route("/test", name="sale_test", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function test(ProductRepository $productRepository): Response
    {

        $user = $this->security->getUser();

        return $this->render('sale/index_test.html.twig', [
            'products' => $productRepository->findByCompany($user->getCompany()),
            'paymentMethods' => $this->paymentMethodRepository->findByCompany($user->getCompany()),
            'categories' => $this->categoryRepository->findByCompany($user->getCompany())
        ]);

    }

    /**
     * @Route("/discountfix", name="sale_discount_fix", methods={"GET"})
     * @param SaleRepository $saleRepository
     * @return JsonResponse
     */
    public function FixDiscounts(SaleRepository $saleRepository) : JsonResponse
    {

        $user = $this->security->getUser();
        $sales = $this->saleRepository->findByCompany($user->getCompany());
        $em = $this->getDoctrine()->getManager();
        foreach($sales as $sale){
           $total_discount = 0;
           $discounts = $sale->getDiscounts();

            foreach($discounts as $discount) {

                $total_discount = $total_discount + $discount->getAmount();

            }

            $sale->setDiscount($total_discount);
            $em->persist($sale);
            $em->flush();

        }
        $returnResponse = new JsonResponse();
        $returnResponse->setjson("{1}");

        return $returnResponse;

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
        $sale = $this->saleRepository->findByCompanyID($user->getCompany(),$id);
        //$discount = $this->discountRepository->findBy(['sale'=>$id]);

        if ($this->container->has('profiler'))
        {
            $this->container->get('profiler')->disable();
        }

        if($user->getCompany()->getId() == 6){
            return $this->render('reciept/reciept.html.twig', [
                'sale' => $sale,
                'products' => $sale->getProducts(),
                'payments'=>$sale->getPayments(),
                'discounts'=>$sale->getProductSoldDiscounts()
            ]);

        }else{
            return $this->render('reciept/reciept_old.html.twig', [
                'sale' => $sale,
                'products' => $sale->getProducts(),
                'payments'=>$sale->getPayments(),
                'discounts'=>$sale->getProductSoldDiscounts()
            ]);

        }

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
        $sale = $this->saleRepository->findByCompanyID($user->getCompany(),$id);

        return $this->render('sale/payment.html.twig', [
            'paymentMethods' => $this->paymentMethodRepository->findAll(),
            'sale' => $sale
        ]);

    }

    /**
     * @Route("/cancel", name="sale_cancel", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function CancelSale(Request $request):JsonResponse
    {
        $user = $this->security->getUser();
        if ($request->getMethod() == 'POST')
        {
         
            $saleId = $request->request->get('saleID');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleId]);

        $productsSold = $this->productSoldRepository->findBy(['sale'=>$saleId]);
      
        $sale->setIsCancelled(true);
        foreach ($productsSold as $productSold){
           $product = $productSold->getProduct();
           $quantity = $product->getQuantity();
           $newQuantity = $quantity + $productSold->getAmount();
           $product->setQuantity($newQuantity);
           $stock = new ProductStock();
            $stock->setCompany($user->getCompany());

            if($quantity < $newQuantity){
                $stock->setAmount($newQuantity - $quantity);
            }else
            {
                $stock->setAmount(($quantity - $newQuantity)/-1);
            }
            $stock->setProduct($product);
            $stock->setTime(new \DateTime());

            $em->persist($stock);
            $em->persist($product);

        }

        $em->persist($sale);
        $em->flush();

        $response = $sale->getId();

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }


    /**
     * @Route("/create", name="create_sale", methods={"POST"})
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
            $client = $request->request->get('client');
            $price = $request->request->get('price');
            $clientCode = $request->request->get('code');
            $productDiscounts = $request->request->get('productDiscountId');
            $discountIds = $request->request->get('discountId');


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
            $client = $this->clientRepository->searchOneByName('Publico En General',$this->security->getUser()->getCompany());
            $sale->setClientId($client);
        }else{
            $sale->setClient($client);
            $clientEntity = $this->clientRepository->searchOneByName($client,$this->security->getUser()->getCompany());
            if($clientEntity == null){
                $clientEntity = new Client();
                $clientEntity->setName($client);
                if($code != null){
                    $clientEntity->setCode($code);
                }
                $em->persist($clientEntity);
                $em->flush();
                $sale->setClientId($clientEntity);
            }else{
                $sale->setClientId($clientEntity);
                $sale->setClient($client);
            }
        }

        $sale->setTime(new \DateTime());
        
        $user = $this->userRepository->findOneByCompanyUsername($this->security->getUser()->getCompany(),$this->session->get('session-user'));
       
        if($user != null){
            $sale->setUser($user);
        }else{
            $sale->setUser($this->security->getUser());
        }
        $sale->setCompany($this->security->getUser()->getCompany());

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
            $productSold->setCompany($this->security->getUser()->getCompany());

            if($product->getPrice()==0){
                $productSold->setPrice($price[$count]);
            }else{
                if($product->getPrice()*$quantity[$count] == $price[$count]){
                    $productSold->setPrice($price[$count]);
                }else{
                    $productSold->setPrice($product->getPrice()*$quantity[$count]);
                    $productSold->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);

                    $discountCount = 0;
                    foreach ($discountIds as $discount){
                        if($productDiscounts[$discountCount] == $prod){
                        $currentDiscount = $this->discountRepository->findByCompanyID($this->security->getUser()->getCompany(),$discount);
                        
                        $productDiscount = new ProductSoldDiscount();
                        $productDiscount->setProductSold($productSold);
                        $productDiscount->setDiscount($currentDiscount);
                        $productDiscount->setSale($sale);
                        $productDiscount->setCompany($this->security->getUser()->getCompany());

                        $totalDiscount = $totalDiscount + $currentDiscount->getAmount();

                        $em->persist($productDiscount);
                        }
                        $discountCount++;
                    }
                    /*
                    foreach ($discounts as $discount){

                        if($discount == $prod){
                            $productDiscount = new Discount();
                            $productDiscount->setProductSold($productSold);
                            $productDiscount->setName($reason[$discountCount]);
                            $productSold->setDiscountReason($reason[$discountCount]);

                            $productDiscount->setAmount($discountAmount[$discountCount]);
                            $productDiscount->setCompany($this->security->getUser()->getCompany());

                            $productDiscount->setSale($sale);
                            $totalDiscount = $totalDiscount + $discountAmount[$discountCount];
                            $em->persist($productDiscount);
                        }
                        $discountCount++;

                        }
                        */
                    $sale->setDiscount($totalDiscount);
                    }
            }

            $em->persist($sale);
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
     * @Route("/edit", name="sale_edit", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function edit(Request $request):JsonResponse
    {
        $totalDiscount = 0;
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
            $saleId = $request->request->get('saleID');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleId]);

        $sale->setTotal($total);
        $sale->setSubtotal($subtotal);
        $sale->setTax($tax);

        if($client == ""){
            $sale->setClient("Publico en General");
        }else{
            $sale->setClient($client);
        }

        $sale->setTime(new \DateTime());
        $sale->setUser($this->security->getUser());
        $sale->setCompany($this->security->getUser()->getCompany());



        $productsSold = $this->productSoldRepository->findBy(['sale'=>$saleId]);
        $count = 0;

        foreach ($productsSold as $productSold){
            if($productSold->getProduct()->getId()){


            }

        $productSold->setAmount($quantity[$count]);
        $productSold->setPrice($price[$count]);
       // $productSold->setDiscount($quantity[$count]);
        $em->persist($productSold);
        $em->flush();

        $count++;
        }

        $em->persist($sale);
        $em->flush();

        $response = $sale->getId();

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/edit2", name="sale_edit2", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function edit2(Request $request):JsonResponse
    {
        $totalDiscount = 0;
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
            $saleId = $request->request->get('saleID');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleId]);

        $sale->setTotal($total);
        $sale->setSubtotal($subtotal);
        $sale->setTax($tax);

        if($client == ""){
            $sale->setClient("Publico en General");
        }else{
            $sale->setClient($client);
        }

        $sale->setTime(new \DateTime());
        $sale->setUser($this->security->getUser());

        $em->persist($sale);
        $em->flush();

        $productsSold = $this->productSoldRepository->findBy(['sale'=>$saleId]);
        $discountsGet = $this->discountRepository->findBy(['sale'=>$saleId]);

        $count = 0;

        foreach ($discountsGet as $discount){
            $em->remove($discount);
            $em->flush();
        }

        foreach ($productsSold as $productSold){
            $product = $productSold->getProduct();
            $product->setQuantity($product->getQuantity() + $productSold->getAmount());

            $em->persist($product);
            $em->remove($productSold);
            $em->flush();
        }

        foreach ($products as $prod){
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
                $productSold->setCompany($this->security->getUser()->getCompany());

            }else{
                if($product->getPrice()*$quantity[$count]== $price[$count]){
                    $productSold->setPrice($price[$count]);
                    $productSold->setCompany($this->security->getUser()->getCompany());

                }else{
                    $productSold->setPrice($product->getPrice()*$quantity[$count]);
                    $productSold->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);
                    $productSold->setCompany($this->security->getUser()->getCompany());

                    $discountCount = 0;

                    foreach ($discounts as $discount){

                        if($discount == $prod){
                            $productDiscount = new Discount();
                            $productDiscount->setProductSold($productSold);
                            $productDiscount->setName($reason[$discountCount]);
                            $productDiscount->setAmount($discountAmount[$discountCount]);
                            $productDiscount->setCompany($this->security->getUser()->getCompany());

                            $productDiscount->setSale($sale);
                            $totalDiscount = $totalDiscount + $discountAmount[$discountCount];
                            $em->persist($productDiscount);
                            
                        }
                        $discountCount++;

                    }
                    $sale->setDiscount($totalDiscount);
                }
            }

            $em->persist($sale);
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
     * @Route("/edit4", name="sale_edit4", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function edit4(Request $request):JsonResponse
    {
        $totalDiscount = 0;
        if ($request->getMethod() == 'POST')
        {
            $total = $request->request->get('total');
            $subtotal = $request->request->get('subtotal');
            $tax = $request->request->get('tax');
            $products = $request->request->get('products');
            $quantity = $request->request->get('quantity');
            $client = $request->request->get('client');
            $price = $request->request->get('price');
            $clientCode = $request->request->get('code');
            $productDiscounts = $request->request->get('productDiscountId');
            $discountIds = $request->request->get('discountId');
            $saleId = $request->request->get('saleID');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleId]);

        $sale->setTotal($total);
        $sale->setSubtotal($subtotal);
        $sale->setTax($tax);

        if($client == ""){
            $sale->setClient("Publico en General");
            $client = $this->clientRepository->searchOneByName('Publico En General',$this->security->getUser()->getCompany());
            $sale->setClientId($client);
        }else{
            $sale->setClient($client);
            $clientEntity = $this->clientRepository->searchOneByName($client,$this->security->getUser()->getCompany());
            if($clientEntity == null){
                $clientEntity = new Client();
                $clientEntity->setName($client);
                if($code != null){
                    $clientEntity->setCode($code);
                }
                $em->persist($clientEntity);
                $em->flush();
                $sale->setClientId($clientEntity);
            }else{
                $sale->setClientId($clientEntity);
                $sale->setClient($client);
            }
        }

        $sale->setTime(new \DateTime());
        
        $user = $this->userRepository->findOneByCompanyUsername($this->security->getUser()->getCompany(),$this->session->get('session-user'));
       
        if($user != null){
            $sale->setUser($user);
        }else{
            $sale->setUser($this->security->getUser());
        }
        $sale->setCompany($this->security->getUser()->getCompany());

        

        $productsSold = $this->productSoldRepository->findBy(['sale'=>$saleId]);
        $discountsGet = $this->productSoldDiscountRepository->findBy(['sale'=>$saleId]);

        $count = 0;

        foreach ($discountsGet as $discount){
            $em->remove($discount);
            $em->flush();
        }

        foreach ($productsSold as $productSold){
            $product = $productSold->getProduct();
            $product->setQuantity($product->getQuantity() + $productSold->getAmount());

            $em->persist($product);
            $em->remove($productSold);
            $em->flush();
        }
    

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
            $productSold->setCompany($this->security->getUser()->getCompany());

            if($product->getPrice()==0){
                $productSold->setPrice($price[$count]);
            }else{
                if($product->getPrice()*$quantity[$count] == $price[$count]){
                    $productSold->setPrice($price[$count]);
                }else{
                    $productSold->setPrice($product->getPrice()*$quantity[$count]);
                    $productSold->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);

                    $discountCount = 0;
                    foreach ($discountIds as $discount){
                        if($productDiscounts[$discountCount] == $prod){
                        $currentDiscount = $this->discountRepository->findByCompanyID($this->security->getUser()->getCompany(),$discount);
                        
                        $productDiscount = new ProductSoldDiscount();
                        $productDiscount->setProductSold($productSold);
                        $productDiscount->setDiscount($currentDiscount);
                        $productDiscount->setSale($sale);
                        $productDiscount->setCompany($this->security->getUser()->getCompany());

                        $totalDiscount = $totalDiscount + $currentDiscount->getAmount();

                        $em->persist($productDiscount);
                        }
                        $discountCount++;
                    }
                    /*
                    foreach ($discounts as $discount){

                        if($discount == $prod){
                            $productDiscount = new Discount();
                            $productDiscount->setProductSold($productSold);
                            $productDiscount->setName($reason[$discountCount]);
                            $productSold->setDiscountReason($reason[$discountCount]);

                            $productDiscount->setAmount($discountAmount[$discountCount]);
                            $productDiscount->setCompany($this->security->getUser()->getCompany());

                            $productDiscount->setSale($sale);
                            $totalDiscount = $totalDiscount + $discountAmount[$discountCount];
                            $em->persist($productDiscount);
                        }
                        $discountCount++;

                        }
                        */
                    $sale->setDiscount($totalDiscount);
                    }
            }

            $em->persist($sale);
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
     * @Route("/edit3", name="sale_edit3", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function edit3(Request $request):JsonResponse
    {
        $totalDiscount = 0;
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
            $saleId = $request->request->get('saleID');

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $sale = $this->saleRepository->findOneBy(['id'=>$saleId]);

        $sale->setTotal($total);
        $sale->setSubtotal($subtotal);
        $sale->setTax($tax);

        if($client == ""){
            $sale->setClient("Publico en General");
        }else{
            $sale->setClient($client);
        }

        $sale->setTime(new \DateTime());
        $sale->setUser($this->security->getUser());

        $em->persist($sale);
        $em->flush();

        $productsSold = $this->productSoldRepository->findBy(['sale'=>$saleId]);
        $discountsGet = $this->discountRepository->findBy(['sale'=>$saleId]);

        $count = 0;

        foreach ($discountsGet as $discount){
            $em->remove($discount);
            $em->flush();
        }

        foreach ($productsSold as $productSold){
            $product = $productSold->getProduct();
            $product->setQuantity($product->getQuantity() + $productSold->getAmount());

            $em->persist($product);
            $em->remove($productSold);
            $em->flush();
        }

        foreach ($products as $prod){
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
                $productSold->setCompany($this->security->getUser()->getCompany());

            }else{
                if($product->getPrice()*$quantity[$count]== $price[$count]){
                    $productSold->setPrice($price[$count]);
                    $productSold->setCompany($this->security->getUser()->getCompany());

                }else{
                    $productSold->setPrice($product->getPrice()*$quantity[$count]);
                    $productSold->setDiscount(($product->getPrice() * $quantity[$count]) - $price[$count]);
                    $productSold->setCompany($this->security->getUser()->getCompany());

                    $discountCount = 0;

                    foreach ($discounts as $discount){

                        if($discount == $prod){
                            $productDiscount = new Discount();
                            $productDiscount->setProductSold($productSold);
                            $productDiscount->setName($reason[$discountCount]);
                            $productDiscount->setAmount($discountAmount[$discountCount]);
                            $productDiscount->setCompany($this->security->getUser()->getCompany());

                            $productDiscount->setSale($sale);
                            $totalDiscount = $totalDiscount + $discountAmount[$discountCount];
                            $em->persist($productDiscount);
                            
                        }
                        $discountCount++;

                    }
                    $sale->setDiscount($totalDiscount);
                }
            }

            $em->persist($sale);
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
            $payment->setCompany($this->security->getUser()->getCompany());

            $em->persist($payment);


            $em->flush();

            $count++;
        }

        $sale->setCommission($commission);
        $sale->setIsPaid(true);
        $em->persist($sale);
        $em->flush();


        $response = '{"id":"'.$sale->getId().'"}';

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchproducts", name="fetch_products", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function fetchProducts(Request $request, ProductRepository $repository):JsonResponse
    {
        if ($request->getMethod() == 'POST')
        {
            $start = $request->request->get('start');
            $length = $request->request->get('length');

        }
        else {
            die();
        }


        $user = $this->security->getUser();
        $results = $this->productRepository->findDataTable($start, $length,$user->getCompany());
        $total_objects_count = $this->productRepository->countElements($user->getCompany());

        $objects = $results["results"];


        $filtered_objects_count = $results["countResult"];


        $response = '{"recordsTotal": '.$total_objects_count.',"recordsFiltered": '.$filtered_objects_count.',"data": ';



        $response .= json_encode($objects);

        $response .= '}';


        $returnResponse = new JsonResponse();
        $returnResponse->setjson(json_encode($objects));
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

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('upc');
        }
        else {
            die();
        }

        $results = $this->productRepository->findOneBy(['upc'=>$id,'company'=>$user->getCompany()]);

        if($results != null){

            $response = '{"id":"'.$results->getId().'","name":"'.$results->getName().'","tax":"'.$results->getIsTaxable().'","price":"'.$results->getPrice().'"}';

        }else{
            $response = 1;
        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchproductsku", name="fetch_product_sku", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchProductSKU(Request $request, ProductRepository $repository):JsonResponse
    {

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('sku');
        }
        else {
            die();
        }

        $results = $this->productRepository->findOneBy(['sku'=>$id,'company'=>$user->getCompany()]);

        if($results != null){
            $name = addslashes($results->getName());
            $response = '{"id":"'.$results->getId().'","name":"'.$name.'","tax":"'.$results->getIsTaxable().'","price":"'.$results->getPrice().'"}';

        }else{
            $response = 1;
        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

     /**
     * @Route("/fetchproductssku", name="fetch_products_sku", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchProductsSKU(Request $request, ProductRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $sku = $request->request->get('sku');
        }
        else {
            die();
       }

        $objects = $this->productRepository->searchBySKU($sku,$user->getCompany());

        $response = json_encode($objects);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchproductname", name="fetch_product_name", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchProductName(Request $request, ProductRepository $repository):JsonResponse
    {

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('name');
        }
        else {
            die();
        }

        $results = $this->productRepository->findOneBy(['name'=>$id,'company'=>$user->getCompany()]);

        if($results != null){
            $name = addslashes($results->getName());
            $response = '{"id":"'.$results->getId().'","name":"'.$name.'","tax":"'.$results->getIsTaxable().'","cost":"'.$results->getCost().'","price":"'.$results->getPrice().'"}';

        }else{
            $response = 1;
        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    

    /**
     * @Route("/fetchproductsname", name="fetch_products_name", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchProductsName(Request $request, ProductRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');
        }
        else {
            die();
       }

        $objects = $this->productRepository->searchByName($name,$user->getCompany());

        $response = json_encode($objects);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }



}
