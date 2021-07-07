<?php

namespace App\Controller;

use App\Entity\ProjectArea;
use App\Repository\AreaRepository;
use App\Repository\SaleRepository;
use Doctrine\ORM\NoResultException;
use App\Repository\PatientRepository;
use App\Repository\ProductRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProjectAreaRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @Route("/sales", name="fetch_sales", methods={"POST"})
     * @param Request $request
     * @param SaleRepository $repository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function fetchSales(Request $request, SaleRepository $repository):JsonResponse
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
        $results = $repository->findDataTable($start, $length,$search,$orders,$columns,$user->getCompany());
        $total_objects_count = $repository->countElements($user->getCompany());

        $objects = $results["results"];


        $filtered_objects_count = $results["countResult"];


        $response = '{"draw": '.$draw.',"recordsTotal": '.$total_objects_count.',"recordsFiltered": '.$filtered_objects_count.',"data": ';



        $response .= json_encode($objects);

        $response .= '}';


        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchareasname", name="fetch_areas_name", methods={"POST"})
     * @param Request $request
     * @param AreaRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchAreasName(Request $request, AreaRepository $repository):JsonResponse
    {
        $user = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
            $name = $request->request->get('name');
        }
        else {
            die();
       }

        $objects = $repository->searchByName($name,$user->getCompany());

        $response = json_encode($objects);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/fetchareaname", name="fetch_area_name", methods={"POST"})
     * @param Request $request
     * @param AreaRepository $repository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchAreaName(Request $request, AreaRepository $areaRepository):JsonResponse
    {

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('name');
        }
        else {
            die();
        }

        $results = $areaRepository->findOneBy(['name'=>$id,'company'=>$user->getCompany()]);

        if($results != null){
            $name = addslashes($results->getName());
            $response = '{"id":"'.$results->getId().'","name":"'.$name.'"}';

        }else{
            $response = -1;
        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/projectaddarea", name="project_add_area", methods={"POST"})
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param AreaRepository $areaRepository
     * @param ProjectAreaRepository $projectAreaRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function ProjectAddArea(Request $request, ProjectRepository $projectRepository, AreaRepository $areaRepository, ProjectAreaRepository $projectAreaRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $areaID = $request->request->get('areaID');
            $projectID = $request->request->get('projectID');
        }
        else {
            die();
        }

        $project = $projectRepository->findOneByCompanyID($user->getCompany(),$projectID);
        $area = $areaRepository->findOneByCompanyID($user->getCompany(),$areaID);

       
        if($project != null && $area != null){
            
            $projectArea = new ProjectArea();
            $projectArea->setProject($project);
            $projectArea->setArea($area);
            $projectArea->setCompany($project->getCompany());

            $em->persist($projectArea);
            $em->flush();
            $response = $projectArea->getId();

        }else{

            $response = -1;

        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

}
