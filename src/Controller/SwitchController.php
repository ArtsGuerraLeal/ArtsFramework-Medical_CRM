<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/switch")
 */
class SwitchController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    private $security;

    private $session;


    public function __construct(UserRepository $userRepository,Security $security, SessionInterface $session){
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->session = $session;
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
     * @Route("/switchuser", name="switch_user", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function switchUser(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $username = $request->request->get('username');
            $pin = $request->request->get('pin');

        }
        else {
            die();
        }


        $results = $this->userRepository->findOneByCompanyUsername($this->security->getUser()->getCompany(),$username);

        if($pin == $results->getPin()){
            $this->session->set('session-user',$results->getUsername());

            $response = '{"id":"'.$results->getId().'","username":"'.$results->getUsername().'","firstName":"'.$results->getFirstname().'","lastName":"'.$results->getLastname().'"}';
    

        }else{

            $response = '{"id":"-1"}';
    
        }

        
        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/getuserswitch", name="get_user_switch", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function GetUserSwitch(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $username = $this->session->get('session-user');

        }
        else {
            die();
        }


        $results = $this->userRepository->findOneByCompanyUsername($this->security->getUser()->getCompany(),$username);

        $response = '{"id":"'.$results->getId().'","username":"'.$results->getUsername().'","firstName":"'.$results->getFirstname().'","lastName":"'.$results->getLastname().'"}';

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }
}
