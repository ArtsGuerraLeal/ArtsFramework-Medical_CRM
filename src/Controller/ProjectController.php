<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Entity\ProjectProduct;
use App\Repository\AreaRepository;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProjectAreaRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ProjectProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{

    private $security;
    
    /**
      * @var EntityManagerInterface
      */
     private $entityManager;

      /**
      * @var ProjectRepository
      */
      private $projectRepository;

      /**
      * @var ClientRepository
      */
      private $clientRepository;

      /**
      * @var UserRepository
      */
      private $userRepository;
 
 
     public function __construct(ProjectRepository $projectRepository, ClientRepository $clientRepository, UserRepository $userRepository, DiscountRepository $discountRepository, CategoryRepository $categoryRepository, ProductRepository $productRepository, EntityManagerInterface $entityManager,PaymentMethodRepository $paymentMethodRepository, Security $security){
         $this->entityManager = $entityManager;
         $this->security = $security;
         $this->productRepository = $productRepository;
         $this->paymentMethodRepository = $paymentMethodRepository;
         $this->categoryRepository = $categoryRepository;
         $this->discountRepository = $discountRepository;
         $this->projectRepository = $projectRepository;
         $this->UserRepository = $userRepository;
     }

     
    /**
     * @Route("/", name="project_index", methods={"GET"})
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/app", name="project_app", methods={"GET"})
     */
    public function app(Project $project): Response
    {

        $user = $this->security->getUser();

        return $this->render('project/app.html.twig', [
            'project' => $project,
            'products' => $this->productRepository->findByCompany($user->getCompany()),
            'paymentMethods' => $this->paymentMethodRepository->findByCompany($user->getCompany()),
            'categories' => $this->categoryRepository->findByCompany($user->getCompany()),
            'discounts' =>  $this->discountRepository->findByCompany($user->getCompany())
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $user = $this->security->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project->setCompany($user->getCompany());
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     */
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fetchareaproducts", name="fetch_area_products", methods={"POST"})
     * @param Request $request
     * @param AreaRepository $areaRepository
     * @param ProjectRepository $projectRepository
     * @param ProjectProductRepository $projectProductRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function fetchAreaProducts(Request $request, AreaRepository $areaRepository, ProjectRepository $projectRepository, ProjectProductRepository $projectProductRepository):JsonResponse
    {

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $projectID = $request->request->get('projectID');
            $areaID = $request->request->get('areaID');
        }
        else {
            die();
        }

        $results = $projectProductRepository->FindProductsInArea($user->getCompany(),$areaID,$projectID);

        if($results != null){
            $response = json_encode($results);
            //$name = addslashes($results->getName());
            //$response = '{"id":"'.$results->getId().'","name":"'.$name.'"}';

        }else{
            $response = -1;
        }



        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/projectaddproduct", name="project_add_product", methods={"POST"})
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param AreaRepository $areaRepository
     * @param AreaRepository $areaRepository
     * @param ProjectAreaRepository $projectAreaRepository
     * @param ProjectProductRepository $projectProductRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function ProjectAddProduct(Request $request, ProductRepository $productRepository, ProjectRepository $projectRepository, AreaRepository $areaRepository, ProjectAreaRepository $projectAreaRepository, ProjectProductRepository $projectProductRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $areaID = $request->request->get('areaID');
            $projectID = $request->request->get('projectID');
            $productID = $request->request->get('productID');
            $productAmount = $request->request->get('amount');

        }
        else {
            die();
        }

        $project = $projectRepository->findOneByCompanyID($user->getCompany(),$projectID);
        $area = $areaRepository->findOneByCompanyID($user->getCompany(),$areaID);
        $product = $productRepository->findOneByCompanyID($user->getCompany(),$productID);

        if($project != null && $area != null && $product != null){
    
            $projectProduct = new ProjectProduct();
            $projectProduct->setProject($project);
            $projectProduct->setArea($area);
            $projectProduct->setCompany($project->getCompany());
            $projectProduct->setProduct($product);
            $projectProduct->setAmount($productAmount);
            $projectProduct->setPrice($product->getPrice()*$productAmount);

            $em->persist($projectProduct);
            $em->flush();
            $response = $projectProduct->getId();

        }else{

            $response = -1;

        }

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/projectremoveproduct", name="project_remove_product", methods={"POST"})
     * @param Request $request
     * @param ProjectRepository $projectRepository
     * @param AreaRepository $areaRepository
     * @param AreaRepository $areaRepository
     * @param ProjectAreaRepository $projectAreaRepository
     * @param ProjectProductRepository $projectProductRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function ProjectRemoveProduct(Request $request, ProductRepository $productRepository, ProjectRepository $projectRepository, AreaRepository $areaRepository, ProjectProductRepository $projectProductRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
           // $areaID = $request->request->get('areaID');
           // $projectID = $request->request->get('projectID');
           // $productID = $request->request->get('productID');
            //$productAmount = $request->request->get('amount');
            $projectProductID = $request->request->get('projectProductID');
        }
        else {
            die();
        }

        $projectProduct = $projectProductRepository->findOneByCompanyID($user->getCompany(),$projectProductID);      

        if($projectProduct != null){
          
            $em->remove($projectProduct);
         //   $em->persist($projectProduct);
            $em->flush();
            $response = 1;

        }else{

            $response = -1;

        }

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/{id}", name="project_delete", methods={"POST"})
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    

    
}
