<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Stripe\Customer;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;


class RegistrationController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository, EntityManagerInterface $entityManager){

        $this->companyRepository = $companyRepository;
        $this->entityManager = $entityManager;

    }

    /**
     * @Route("/{companyName}/register", name="register_provider")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param CompanyRepository $companyRepository
     * @param $companyName
     * @return RedirectResponse|Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function registerAtCompany(Request $request,UserPasswordEncoderInterface $passwordEncoder, CompanyRepository $companyRepository,$companyName)
    {
        $company = $companyRepository->findOneBy(['name' => $companyName]);

        Stripe::setApiKey('sk_test_HHOQhx8Nk5r0LJGDUaxYlfRK004xJe9Yiv');
        $form = $this->createFormBuilder()
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class, ['label' => false])
            ->add('username', TextType::class, ['label' => false])
            ->add('email', EmailType::class, ['label' => false])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('register',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ]
            ])
            ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user,$data['password'])
            );
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);

            $user->setRegisterdate(new \DateTime('now',new \DateTimeZone('America/Mexico_City')));


            if($company != null){
                $user->setCompany($company);
                $user->setRoles(['ROLE_UNAUTHORIZED_USER']);

                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('app_login'));

            }else{
                $this->addFlash('error', 'Company doesnt exist');
            }


        }
        if($company != null) {
            return $this->render('registration/client_register.html.twig', [
                'form' => $form->createView(),
                'company' => $companyName
            ]);
        }else{
            return $this->redirect($this->generateUrl('landing.page'));


        }
    }



    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GoogleAuthenticatorInterface $authenticator
     * @param CompanyRepository $companyRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function register(Request $request,UserPasswordEncoderInterface $passwordEncoder, GoogleAuthenticatorInterface $authenticator, CompanyRepository $companyRepository)
    {
        Stripe::setApiKey('sk_test_HHOQhx8Nk5r0LJGDUaxYlfRK004xJe9Yiv');
        $form = $this->createFormBuilder()
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class, ['label' => false])
            ->add('username', TextType::class, ['label' => false])
            ->add('email', EmailType::class, ['label' => false])
            ->add('password',RepeatedType::class,[
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('create',ButtonType::class,[
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
                    'onclick'=>"openTab(event,'Tab1')"
                ]
            ])
            ->add('companyname', TextType::class,[
                'label'  => 'Company Name',
                    'required'=>false
                ]
            )
            ->add('join',ButtonType::class,[
                'attr' => [
                    'class' => 'btn btn-primary btn-block',
                    'onclick'=>"openTab(event,'Tab2')"
                ]
            ])
            ->add('companyid', NumberType::class,[
                'label'  => 'Company ID',
                    'required'=>false
                ]
            )
            ->add('register',SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary btn-block'
                ]
            ])
            ->getForm();


        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setUsername($data['username']);
            $user->setPassword(
                $passwordEncoder->encodePassword($user,$data['password'])
            );
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);

            $user->setRegisterdate(new \DateTime('now',new \DateTimeZone('America/Mexico_City')));

            if (!empty($data['companyname']) && !empty($data['companyid'])){
                $this->addFlash('error', 'Both Join and  Create fields filled! Only fill one');
            }else {
                if (!empty($data['companyname'])) {

                    $user->setRoles(['ROLE_COMPANY_ADMIN']);
                    $company = new Company();
                    $company->setName($data['companyname']);
                    $company->setCode(md5(uniqid()));


                    $customer = Customer::create([
                        'name' => $data['companyname'],
                        'email' => $data['email']
                    ]);

                    $company->setStripeId($customer['id']);


                    $em->persist($company);
                    $em->flush();
                    $user->setCompany($company);
                } elseif (!empty($data['companyid'])) {
                    $company = $companyRepository->findOneBy(['id' => $data['companyid']]);
                    $user->setCompany($company);
                    $user->setRoles(['ROLE_UNAUTHORIZED_USER']);
                }

                //  $secret = $authenticator->generateSecret();
                //     $user->setGoogleAuthenticatorSecret($secret);


                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('app_login'));
            }
        }
        return $this->render('registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
