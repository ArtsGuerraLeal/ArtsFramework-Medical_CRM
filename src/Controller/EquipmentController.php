<?php

namespace App\Controller;

use App\Entity\Equipment;
use App\Entity\Treatment;
use App\Form\EquipmentType;
use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/equipment")
 */
class EquipmentController extends AbstractFOSRestController
{
    /**
     * @var EquipmentRepository
     */
    private $equipmentRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $security;

    
    public function __construct(EquipmentRepository $equipmentRepository , EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->equipmentRepository = $equipmentRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="equipment_index", methods={"GET"})
     * @param EquipmentRepository $equipmentRepository
     * @return Response
     */
    public function index( EquipmentRepository $equipmentRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('equipment/index.html.twig', [
            'equipment' => $equipmentRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="equipment_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $equipment = new Equipment();

        $user = $this->security->getUser();
        $equipment->setCompany($user->getCompany());

        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($equipment);
            $entityManager->flush();

            return $this->redirectToRoute('equipment_index');
        }

        return $this->render('equipment/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="equipment_show", methods={"GET"})
     * @param EquipmentRepository $equipmentRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(EquipmentRepository $equipmentRepository, $id): Response
    {
        $user = $this->security->getUser();
        $equipment = $equipmentRepository->findOneByCompanyID($user->getCompany(), $id);
        return $this->render('equipment/show.html.twig', [
            'equipment' => $equipment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="equipment_edit", methods={"GET","POST"})
     * @param EquipmentRepository $equipmentRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(EquipmentRepository $equipmentRepository,$id,Request $request ): Response
    {
        $user = $this->security->getUser();
        $company = $user->getCompany();
        $entityManager = $this->getDoctrine()->getManager();
        $equipment = $equipmentRepository->findOneByCompanyID($user->getCompany(), $id);

        $originalTreatment = new ArrayCollection();

        foreach ($equipment->getTreatment() as $treatment) {
            $originalTreatment->add($treatment);
        }

        $form = $this->createForm(EquipmentType::class, $equipment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // remove the relationship between the Treatment and the Equipment
            foreach ($originalTreatment as $treatment) {
                if (false === $equipment->getTreatment()->contains($treatment)) {

                    $entityManager->remove($treatment);

                    $entityManager->persist($treatment);

                }
            }
            $entityManager->persist($equipment);
            $entityManager->flush();

            return $this->redirectToRoute('equipment_index');
        }

        return $this->render('equipment/edit.html.twig', [
            'equipment' => $equipment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="equipment_delete", methods={"DELETE"})
     * @param Request $request
     * @param Equipment $equipment
     * @return Response
     */
    public function delete( Request $request, Equipment $equipment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($equipment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipment_index');
    }

    public function getEquipmentActions(Equipment $equipment){
        return $this->view($equipment,Response::HTTP_OK);
    }

    public function getEquipmentsActions(){
        $data = $this->equipmentRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="name", description="Line 1", nullable=false)
     * @Rest\RequestParam(name="cost", description="Line 2", nullable=true)
     * @Rest\RequestParam(name="purchasedOn", description="Postal Code", nullable=true)
     * @Rest\RequestParam(name="lastUsedOn", description="City", nullable=true)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postEquipmentAction(ParamFetcher $paramFetcher){
        $name = $paramFetcher->get('name');
        $cost = $paramFetcher->get('cost');
        $purchased_on = $paramFetcher->get('purchasedOn');
        $last_used_on = $paramFetcher->get('lastUsedOn');

        if($name){
            $equipment = new Equipment();
            $equipment->setName($name);
            $equipment->setCost($cost);
            $equipment->setPurchasedOn($purchased_on);
            $equipment->setLastUsedOn($last_used_on);
            $this->entityManager->persist($equipment);
            $this->entityManager->flush();
            return $this->view($equipment,Response::HTTP_CREATED);

        }
        return $this->view(['name' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }


}
