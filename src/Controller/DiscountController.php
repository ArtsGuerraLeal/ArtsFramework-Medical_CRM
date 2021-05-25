<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Form\DiscountType;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/discount")
*/
class DiscountController extends AbstractController
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
     * @var DiscountRepository
     */
    private $discountRepository;


    public function __construct(DiscountRepository $discountRepository, EntityManagerInterface $entityManager,Security $security){
        $this->discountRepository = $discountRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;

    }
    
    /**
     * @Route("/", name="discount_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $user = $this->security->getUser();

        $discounts = $this->discountRepository->findByCompany($user->getCompany());
        return $this->render('discount/index.html.twig', [
            'controller_name' => 'DiscountController',
            'discounts' => $discounts
        ]);
    }

    

    /**
     * @Route("/new", name="discount_new", methods={"GET","POST"})
     * @return Response
     */
    public function new(Request $request): Response
    {

        $user = $this->security->getUser();

        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $discount->setCompany($user->getCompany());

           $entityManager->persist($discount);
           $entityManager->flush();

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="discount_show", methods={"GET"})
     * @param DiscountRepository $discountRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(DiscountRepository $discountRepository, $id): Response

    {
        $user = $this->security->getUser();
        $discount = $discountRepository->findByCompanyID($user->getCompany(), $id);
        return $this->render('discount/show.html.twig', [
            'discount' => $discount,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="discount_edit", methods={"GET","POST"})
     * @param Request $request
     * @param DiscountRepository $discountRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, DiscountRepository $discountRepository, $id): Response
    {
        $user = $this->security->getUser();
        $discount = $discountRepository->findByCompanyID($user->getCompany(), $id);

        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/edit.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fetchproducts", name="fetch_products", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $repository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function fetchProducts(Request $request, DiscountRepository $repository):JsonResponse
    {
        if ($request->getMethod() == 'POST')
        {

        }
        else {
            die();
        }


        $user = $this->security->getUser();
        $results = $this->discountRepository->findByCompanyArray($user->getCompany());

        $response = json_encode($results);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }


}
