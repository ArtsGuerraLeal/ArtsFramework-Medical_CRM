<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/switch")
 */
class SwitchController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    private  $security;

    public function __construct(UserRepository $userRepository,Security $security){
        $this->userRepository = $userRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="switch")
     */
    public function index()
    {
        return $this->render('switch/index.html.twig', [
            'controller_name' => 'SwitchController',
        ]);
    }


    /**
     * @Route("/{username}", name="app_switch_user", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function switch(User $user): Response
    {

        if($this->getUser()->getCompany() == $user->getCompany()){
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_login');
    }
}
