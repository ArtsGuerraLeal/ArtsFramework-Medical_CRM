<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Exception;

class ClientController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var ClientRepository
     */
    private $clientRepository;


    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager,Security $security){
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;

    }

    /**
     * @Route("/client", name="client")
     */
    public function index()
    {
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }

    /**
     * @Route("/fetchclients", name="fetch_clients", methods={"POST"})
     * @param Request $request
     * @param ClientRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchClients(Request $request, ClientRepository $repository):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');
        }
        else {
            die();
       }

        $objects = $this->clientRepository->findAllByName($name,3);

        $response = '{';


        $response .= '}';
        $response = json_encode($objects);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/createclient", name="create_client", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function createClient(Request $request):JsonResponse
    {
        $user = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');

        }
        else {
            die();
        }

        $em = $this->getDoctrine()->getManager();


       $client = new Client();


        $client->setCompany($user->getCompany());
        $client->setName($name);


        $em->persist($client);
        $em->flush();


        $response = "1";

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }
}
