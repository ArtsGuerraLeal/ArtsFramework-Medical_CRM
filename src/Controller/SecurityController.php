<?php

namespace App\Controller;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
        //    var_dump($this->getUser());
             return $this->redirectToRoute('home.index');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/2fa", name="2fa_login")
     * @param GoogleAuthenticatorInterface $authenticator
     * @param TokenStorageInterface $storage
     * @return Response
     */
    public function check2fa(GoogleAuthenticatorInterface $authenticator, TokenStorageInterface $storage){
        $code = $authenticator->getQRContent($storage->getToken()->getUser());
        $qrCode =  "http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl=" . urlencode($code);

        if(!$code){
            $this->addFlash('error', 'Both Join and  Create fields filled! Only fill one');
        }

        return $this->render('security/2fa_form.html.twig', ['qrCode' => $qrCode]);

    }
}
