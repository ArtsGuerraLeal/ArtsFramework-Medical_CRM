<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\InvoiceItem;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;


class PaymentController extends AbstractController
{


    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security){

        $this->security = $security;

    }


    /**
     * @Route("/payment", name="payment")
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return Response
     */
    public function index(Request $request,CompanyRepository $companyRepository)
    {
        $user = $this->security->getUser();
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);

        Stripe::setApiKey('sk_test_HHOQhx8Nk5r0LJGDUaxYlfRK004xJe9Yiv');

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    /**
     * @Route("/payment/new", name="payment_new")
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return Response
     * @throws ApiErrorException
     */
    public function new(Request $request,CompanyRepository $companyRepository){
        Stripe::setApiKey('sk_test_HHOQhx8Nk5r0LJGDUaxYlfRK004xJe9Yiv');

        $user = $this->security->getUser();
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);
        $form = $this->createFormBuilder()
            ->add('token', HiddenType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('submit', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $array = json_decode($form->get('token')->getData(),true);

            $customer = Customer::create([
                'payment_method' => $array['paymentMethod']['id'],
                'email' => $user->getEmail(),
                'invoice_settings' => [
                    'default_payment_method' => $array['paymentMethod']['id']
                ]
            ]);

            Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'plan' => 'plan_Gl6vUKhTvbbBvK',
                    ],
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'collection_method' => 'send_invoice',
                'days_until_due' => 30,
            ]);

              return $this->redirectToRoute('home.index');
        }


        return $this->render('payment/new.html.twig', [
            'form' => $form->createView(),

        ]);

    }


    /**
     * @Route("/payment/test", name="payment_test")

     * @throws ApiErrorException
     */
    public function Test(){

        Stripe::setApiKey('sk_test_HHOQhx8Nk5r0LJGDUaxYlfRK004xJe9Yiv');

            InvoiceItem::create([
                'amount' => 30.00,
                'currency' => 'usd',
                'customer' => 'cus_GlUJfOjC1toIL4',
                'description' => 'Setup Fee',
            ]);

        $invoice = Invoice::create([
            'customer' => 'cus_GlUJfOjC1toIL4',
            'collection_method' => 'send_invoice',
            'days_until_due' => 30,
        ]);

        $invoice->sendInvoice();

        return $this->render('payment/index.html.twig', [


        ]);
    }





}
