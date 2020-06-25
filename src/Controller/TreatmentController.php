<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Treatment;
use App\Form\TreatmentType;
use App\Repository\AppointmentRepository;
use App\Repository\EquipmentRepository;
use App\Repository\TreatmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/treatment")
 */
class TreatmentController extends AbstractFOSRestController
{

    /**
     * @var TreatmentRepository
     */
    private $treatmentRepository;
    /**
     * @var EquipmentRepository
     */
    private $equipmentRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $security;


    public function __construct(EquipmentRepository $equipmentRepository, TreatmentRepository $treatmentRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->equipmentRepository = $equipmentRepository;
        $this->treatmentRepository = $treatmentRepository;
        $this->security = $security;

    }

    /**
     * @Route("/available", name="treatment_fetch_available", methods={"GET"})
     * @param Request $request
     * @param TreatmentRepository $repository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */

    public function fetchAvailable(Request $request, AppointmentRepository $repository): JsonResponse
    {
        //http://localhost/treatment/available?from=2020-02-20T03:42:00-06:00&to=2020-02-20T13:42:00-06:00
        $user = $this->security->getUser();

           $from = $request->query->get("from");
           $to = $request->query->get("to");

        $result = $repository->findBetweenDates($user->getCompany(),$from,$to);

        //$result = $repository->findByCompany2($user->getCompany());
        dump($this->json($result));
        return $this->json($result);

    }


    /**
     * @Route("/", name="treatment_index", methods={"GET"})
     * @param TreatmentRepository $treatmentRepository
     * @return Response
     */
    public function index(TreatmentRepository $treatmentRepository): Response
    {
        $user = $this->security->getUser();

        return $this->render('treatment/index.html.twig', [
            'treatments' => $treatmentRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="treatment_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->security->getUser();
        $treatment = new Treatment();
        $treatment->setCompany($user->getCompany());
        $form = $this->createForm(TreatmentType::class, $treatment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($treatment);
            $entityManager->flush();

            return $this->redirectToRoute('treatment_index');
        }

        return $this->render('treatment/new.html.twig', [
            'treatment' => $treatment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="treatment_show", methods={"GET"})
     * @param TreatmentRepository $treatmentRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(TreatmentRepository $treatmentRepository, $id): Response

    {
        $user = $this->security->getUser();
        $treatment = $treatmentRepository->findOneByCompanyID($user->getCompany(), $id);
        return $this->render('treatment/show.html.twig', [
            'treatment' => $treatment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="treatment_edit", methods={"GET","POST"})
     * @param Request $request
     * @param TreatmentRepository $treatmentRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, TreatmentRepository $treatmentRepository, $id): Response
    {
        $user = $this->security->getUser();
        $treatment = $treatmentRepository->findOneByCompanyID($user->getCompany(), $id);

        $form = $this->createForm(TreatmentType::class, $treatment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('treatment_index');
        }

        return $this->render('treatment/edit.html.twig', [
            'treatment' => $treatment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="treatment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Treatment $treatment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$treatment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($treatment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('treatment_index');
    }

    public function getTreatmentActions(Treatment $treatment){
        return $this->view($treatment,Response::HTTP_OK);
    }

    public function getTreatmentsActions(){
        $data = $this->treatmentRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    public function getTreatmentEquipmentActions(Equipment $equipment){
        return $this->view($equipment,Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="name", description="Line 1", nullable=false)
     * @Rest\RequestParam(name="cost", description="Line 2", nullable=true)
     * @Rest\RequestParam(name="equipmentId", description="City", nullable=true)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postTreatmentAction(ParamFetcher $paramFetcher){
        $name = $paramFetcher->get('name');
        $cost = $paramFetcher->get('cost');
        $equipmentId = $paramFetcher->get('equipmentId');
        $equipment = $this->equipmentRepository->findOneBy(['id' => $equipmentId]);

        if($name){
            $treatment = new Treatment();
            $treatment->setName($name);
            $treatment->setCost($cost);
            $treatment->setEquipment($equipment);
            $this->entityManager->persist($treatment);
            $this->entityManager->flush();
            return $this->view($treatment,Response::HTTP_CREATED);

        }
        return $this->view(['name' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }


}
