<?php

namespace App\Controller;

use App\Repository\PatientRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/fetch")
 */
class FetchController extends AbstractController
{

    private $security;
    /**
     * @var PatientRepository
     */
    private $patientRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(PatientRepository $patientRepository, EntityManagerInterface $entityManager,Security $security, ProductRepository $productRepository){
        $this->entityManager = $entityManager;
        $this->patientRepository = $patientRepository;
        $this->productRepository = $productRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="fetch_index", methods={"GET"})
     * @return Response
     */
    public function index():Response
    {
        return $this->render('fetch/index.html.twig', [
            'controller_name' => 'FetchController',
        ]);
    }

    /**
     * @Route("/available", name="fetch_patients", methods={"POST"})
     * @param Request $request
     * @param PatientRepository $repository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function fetchAvailable(Request $request, PatientRepository $repository):JsonResponse
    {
        if ($request->getMethod() == 'POST')
        {
            $draw =  intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $columns = $request->request->get('columns');
            $orders = $request->request->get('order');

        }
        else {
           die();
        }

        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        $user = $this->security->getUser();
        $results = $this->patientRepository->findDataTable($start, $length,$search,$orders,$columns,$user->getCompany());
        $total_objects_count = $this->patientRepository->countElements($user->getCompany());

        $objects = $results["results"];


        $filtered_objects_count = $results["countResult"];


        $response = '{"draw": '.$draw.',"recordsTotal": '.$total_objects_count.',"recordsFiltered": '.$filtered_objects_count.',"data": ';



        $response .= json_encode($objects);

        $response .= '}';


        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }









}
