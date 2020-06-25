<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use App\Service\Client;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
class       UserSettingsController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/settings/user", name="user_settings", methods={"GET","POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function index(UserRepository $userRepository,Request $request)
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $dataForm = $this->createFormBuilder();


        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $dataForm->add('firstname', TextType::class);
        $dataForm->add('lastname', TextType::class);
        $dataForm->add('check', CheckboxType::class);

        $dataForm ->add('save', SubmitType::class, ['label' => 'Create Medical']);

        $form = $dataForm->getForm();
        $form->handleRequest($request);
        $jsonContent = $serializer->serialize($form->getData(), 'json');

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                var_dump($jsonContent);
                $user->setSettings(array($jsonContent));
                $entityManager->persist($user);
                $entityManager->flush();

            }
            catch(IOException $e) {
            }

            return $this->redirectToRoute('user_settings');
        }


        return $this->render('user_settings/index.html.twig', [
            'controller_name' => 'UserSettingsController',
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/settings/user/2factor", name="user_2factor", methods={"GET","POST"})
     * @param GoogleAuthenticatorInterface $authenticator
     * @param Request $request
     * @return Response
     */
    public function auth(GoogleAuthenticatorInterface $authenticator, Request $request)
    {
        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();
        $secret = $authenticator->generateSecret();
        $user->setGoogleAuthenticatorSecret($secret);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_qr');
    }

    /**
     * @Route("/settings/user/2factordeauth", name="user_2factor_deauth", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function deauth( Request $request)
    {
        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();

        $user->setGoogleAuthenticatorSecret(null);
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('user_settings');
    }

    /**
     * @Route("/settings/user/qr", name="user_qr", methods={"GET","POST"})
     * @param GoogleAuthenticatorInterface $authenticator
     * @param TokenStorageInterface $storage
     * @param Request $request
     * @return Response
     */
    public function showQR(GoogleAuthenticatorInterface $authenticator, TokenStorageInterface $storage, Request $request)
    {
        $code = $authenticator->getQRContent($storage->getToken()->getUser());
        $qrCode =  "http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=" . urlencode($code);
        return $this->render('user_settings/2fa_form.html.twig', ['qrCode' => $qrCode]);
    }

    /**
     * @Route("/settings/user/google", name="user_google", methods={"GET","POST"})
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return Response
     * @throws Google_Exception
     */
    public function enableGoogle(Request $request, CompanyRepository $companyRepository)
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig('Resources/client_secret.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');


        $dataForm = $this->createFormBuilder();
        $dataForm->add('authCode', TextType::class);
        $dataForm ->add('save', SubmitType::class, ['label' => 'Submit Auth Code']);
        $form = $dataForm->getForm();

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.


        if($company->getGoogleJson() != null){
            $accessToken = json_decode($company->getGoogleJson()[0],true);
            $client->setAccessToken($accessToken);

        }


      /*  $tokenPath = 'token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
            var_dump($accessToken);

        }*/

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    try {
                        $authCode = $form->getData('authCode');

                        // Exchange authorization code for an access token.
                        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode['authCode']);


                        $client->setAccessToken($accessToken);

                        $jsonContent = $serializer->serialize($accessToken, 'json');
                        var_dump(array($jsonContent));

                        // Check to see if there was an error.
                        if (array_key_exists('error', $accessToken)) {
                            throw new Exception(join(', ', $accessToken));
                        }

                        // Save the token to a file.
                        if($company->getGoogleJson() == null){

                            $company->setGoogleJson(array($jsonContent));
                            $entityManager->persist($company);
                            $entityManager->flush();
                        }
                    }
                    catch(IOException $e) {
                    }

                    return $this->redirectToRoute('user_settings');
                }
                return $this->render('user_settings/google.html.twig',[
                    'authUrl' => $authUrl,
                    'form' => $form->createView()]);
            }

        }

        //   $service = new Google_Service_Calendar($client);

        return $this->render('user_settings/google.html.twig',[
            'authUrl' => 'hello',
            'form' => $form->createView()]);
    }


    /**
     * @Route("/settings/user/google2", name="user_google2", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function testGoogle(Request $request, Client $googleClient, CompanyRepository $companyRepository)
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);

        $client = $googleClient->getClient($company->getGoogleJson()[0]);

        $service = new Google_Service_Calendar($client);

// Print the next 10 events on the user's calendar.
        $calendarId = 'primary';
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        if (empty($events)) {
            print "No upcoming events found.\n";
        } else {
            print "Upcoming events:\n";
            foreach ($events as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                }
                printf("%s (%s)\n", $event->getSummary(), $start);
            }
        }
        return $this->render('user_settings/google2.html.twig',['event'=>$events]);


    }




}
