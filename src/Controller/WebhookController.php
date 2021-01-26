<?php

namespace App\Controller;

use App\Entity\ProviderOrder;
use App\Entity\ProductOrdered;
use App\Repository\UserRepository;
use App\Repository\CompanyRepository;
use App\Repository\ProductRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProviderOrderRepository;
use App\Repository\ProductOrderedRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
* @Route("/webhook")
*/
class WebhookController extends AbstractController
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
     * @var ProductOrderedRepository
     */
    private $productOrderedRepository;

    /**
     * @varProviderOrderRepository
     */
    private $providerOrderRepository;

    /**
     * @var ProviderRepository
     */
    private $providerRepository;

     /**
     * @var CompanyRepository
     */
    private $companyRepository;

    private $security;
    
     /**
     * @var UserRepository
     */
    private $userRepository;

    private $session;
    public function __construct(CompanyRepository $companyRepository, ProductOrderedRepository $productOrderedRepository, ProviderOrderRepository $providerOrderRepository, ProviderRepository $providerRepository, UserRepository $userRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager, Security $security,SessionInterface $session){
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->productOrderedRepository = $productOrderedRepository;
        $this->providerOrderRepository = $providerOrderRepository;
        $this->providerRepository = $providerRepository;
        $this->companyRepository = $companyRepository;

    }

    /**
     * @Route("/", name="webhook")
     */
    public function index(): Response
    {
        return $this->render('webhook/index.html.twig', [
            'controller_name' => 'WebhookController',
        ]);
    }

    /**
     * @Route("/{id}/recieveorder", name="shopify_webhook", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function webhook(Request $request,$id): JsonResponse
    {
        if ($request->getMethod() == 'POST')
        {
            $order_id = $request->request->get('order_id');
            $status = $request->request->get('status');
            $items =  $request->request->get('line_items');
            $shippingAddress = $request->request->get('shipping_address');
            $customer = $request->request->get('customer');
            $orderNumber = $request->request->get('order_number');
        }
        else {
            die();
        }

        //if we recieve webhook with data create an order
        $em = $this->getDoctrine()->getManager();

        $order = new ProviderOrder();
        
        $orderTotal = 0;
        
      //  $provider = $this->providerRepository->findByCompany();
        $company = $this->companyRepository->findOneBy(['id'=>5]);
        $order->setCompany($company);
        $order->setTotal($orderTotal);
        $order->setClient($customer['first_name'].' '.$customer['last_name']);
        $order->setTelephone($shippingAddress['phone']);
        $order->setFirstName($shippingAddress['first_name']);
        $order->setLastName($shippingAddress['last_name']);
        $order->setLine1($shippingAddress['address1']);
        if($shippingAddress['address2'] != null){
            $order->setLine2($shippingAddress['address2']);
        }else{
            $order->setLine2('');
        }

        $order->setCity($shippingAddress['city']);
        $order->setState($shippingAddress['province']);
        $order->setPostalCode($shippingAddress['zip']);
        $order->setOrderNumber($orderNumber);
        $order->setTime(new \DateTime());

        $em->persist($order);
        $em->flush();

        
        
        foreach ($items as $item ){

            $product = $this->productRepository->findOneByCompanySKU($company,$item['sku']);
            
            if($product != null){
            $orderTotal = $orderTotal + ($product->getCost() * $item['quantity']);
            $productOrdered = new ProductOrdered();
            $productOrdered->setProduct($product);
            $productOrdered->setProviderOrder($order);
            $productOrdered->setCompany($company);
            $productOrdered->setAmount($item['quantity']);

            $em->persist($productOrdered);
            $em->flush();
            }

        }

        $order->setTotal($orderTotal);
        $em->persist($order);
        $em->flush();
        //$order->setProvider();




        return new JsonResponse(['HTTPStatus' => 200,'Order_id'=>$order_id,'Status'=>$status,'Items'=>count($items)]);
    }
}
