<?php

namespace App\Controller;

use Exception;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/client")
*/
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
     * @Route("/", name="client_index")
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->security->getUser();
        $clients = $this->clientRepository->findByCompany($user->getCompany());
        return $this->render('client/index.html.twig', [
            'clients' => $clients
        ]);
    }

     /**
     * @Route("/{id}", name="client_show", methods={"GET"})
     * @param ClientRepository $clientRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(ClientRepository $clientRepository, $id): Response
    {
        $user = $this->security->getUser();
        $client = $clientRepository->findByCompanyID($user->getCompany(), $id);
        $sales = $client->getSales();

        return $this->render('client/show.html.twig', [
            'client' => $client,
            'sales' => $sales
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET"})
     * @param ClientRepository $clientRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(ClientRepository $clientRepository, $id): Response
    {
        $user = $this->security->getUser();
        $client = $clientRepository->findByCompanyID($user->getCompany(), $id);
      
        return $this->render('client/edit.html.twig', [
            'client' => $client
        ]);
    }

     /**
     * @Route("/editclient", name="client_edit_post", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function EditClient(Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            
            $id = $request->request->get('id');
    
            $name = $request->request->get('name');
            $email = $request->request->get('email');

            $tax_id = $request->request->get('taxid');
            $business = $request->request->get('business');

            $line1 = $request->request->get('line1');
            $line2 = $request->request->get('line2');
            $postalcode = $request->request->get('postalcode');
            $city = $request->request->get('city');
            $state = $request->request->get('state');
            $country = $request->request->get('country');

            $code = $request->request->get('code');
            $type = $request->request->get('type');
            $note = $request->request->get('note');

            $phone = $request->request->get('phone');
            $cellphone = $request->request->get('cellphone');

        }
        else {
            die();
        }

        $user = $this->security->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $client = $this->clientRepository->findByCompanyID($user->getCompany(), $id);
        $client->setName($name);
        $client->setPhone($phone);
        $client->setEmail($email);
        $client->setCellphone($cellphone);
        $client->setBusiness($business);
        $client->setTaxId($tax_id);
        $client->setNote($note);

        $client->setLine1($line1);
        $client->setLine2($line2);
        $client->setCity($city);
        $client->setState($state);
        $client->setCountry($country);
        $client->setPostalcode($postalcode);

        $client->setType($type);
        $client->setCode($code);

        $entityManager->persist($client);
        $entityManager->flush();

    
        $returnResponse = new JsonResponse();
        $returnResponse->setjson(200);

        return $returnResponse;

       
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
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');
        }
        else {
            die();
       }

        $objects = $this->clientRepository->searchByName($name,$user->getCompany());

        $response = '{';


        $response .= '}';
        $response = json_encode($objects);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchclient", name="fetch_client", methods={"POST"})
     * @param Request $request
     * @param ClientRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchClient(Request $request, ClientRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $client = $request->request->get('name');
        }
        else {
            die();
       }

        $results = $this->clientRepository->searchOneByName($client,$this->security->getUser()->getCompany());
        
        if($results != null){
            $response = '{"name":"'.$results->getName().
                '","email":"'.$results->getEmail().
                '","phone":"'.$results->getPhone().
                '","cellphone":"'.$results->getCellphone().
                '","business":"'.$results->getBusiness().
                '","taxid":"'.$results->getTaxID().
                '","note":"'.$results->getNote().
                '","line1":"'.$results->getLine1().
                '","line2":"'.$results->getLine2().
                '","city":"'.$results->getCity().
                '","state":"'.$results->getState().
                '","postalcode":"'.$results->getPostalcode().
                '","country":"'.$results->getCountry().
                '","type":"'.$results->getType().
                '","code":"'.$results->getCode().'"}';

        }else{
            $response = 1;
        }


        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/saveclient", name="save_client", methods={"POST"})
     * @param Request $request
     * @param ClientRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function saveClient(Request $request, ClientRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
            $cellphone = $request->request->get('cellphone');
            $business = $request->request->get('business');
            $taxID = $request->request->get('taxID');
            $line1 = $request->request->get('line1');
            $line2 = $request->request->get('line2');
            $city = $request->request->get('city');
            $state = $request->request->get('state');
            $postalcode = $request->request->get('postalcode');
            $country = $request->request->get('country');
            $type = $request->request->get('type');
            $note = $request->request->get('note');
            $code = $request->request->get('code');

        }
        else {
            die();
       }

        $client = $this->clientRepository->searchOneByName($name,$this->security->getUser()->getCompany());
        
        $em = $this->getDoctrine()->getManager();

        $client->setName($name);
        $client->setEmail($email);
        $client->setPhone($phone);
        $client->setCellphone($cellphone);
        $client->setBusiness($business);
        $client->setTaxID($taxID);
        $client->setLine1($line1);
        $client->setLine2($line2);
        $client->setCity($city);
        $client->setState($state);
        $client->setCountry($country);
        $client->setPostalcode($postalcode);
        $client->setType($type);
        $client->setNote($note);
        $client->setCode($code);

        $em->persist($client);
        $em->flush();

        $response = 1;

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchclientscode", name="fetch_clients_code", methods={"POST"})
     * @param Request $request
     * @param ClientRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchClientsCode(Request $request, ClientRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $code = $request->request->get('code');
        }
        else {
            die();
       }

        $objects = $this->clientRepository->searchByCode($code,$user->getCompany());

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
            $code = $request->request->get('code');

        }
        else {
            die();
        }

        $em = $this->getDoctrine()->getManager();


        $client = new Client();

        $client->setCompany($user->getCompany());
        $client->setName($name);
        if($code != ''){
            $client->setCode($code);
        }

        $em->persist($client);
        $em->flush();

        $response = $client->getId();

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }
}
