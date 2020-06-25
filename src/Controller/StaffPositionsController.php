<?php

namespace App\Controller;

use App\Entity\StaffPositions;
use App\Form\StaffPositionsType;
use App\Repository\StaffPositionsRepository;
use App\Repository\StaffRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/staffpositions")
 */
class StaffPositionsController extends AbstractController
{
    private $security;
    /**
     * @var StaffPositionsRepository
     */
    private $staffPositionsRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(StaffPositionsRepository $staffPositionsRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->staffPositionsRepository = $staffPositionsRepository;

        $this->security = $security;

    }

    /**
     * @Route("/", name="staff_positions_index", methods={"GET"})
     * @param StaffPositionsRepository $staffPositionsRepository
     * @return Response
     */
    public function index(StaffPositionsRepository $staffPositionsRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('staff_positions/index.html.twig', [
            'staff_positions' => $staffPositionsRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="staff_positions_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $staffPosition = new StaffPositions();
        $user = $this->security->getUser();
        $staffPosition->setCompany($user->getCompany());
        $form = $this->createForm(StaffPositionsType::class, $staffPosition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staffPosition);
            $entityManager->flush();

            return $this->redirectToRoute('staff_positions_index');
        }

        return $this->render('staff_positions/new.html.twig', [
            'staff_position' => $staffPosition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="staff_positions_show", methods={"GET"})
     * @param StaffPositionsRepository $staffPositionsRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(StaffPositionsRepository $staffPositionsRepository, $id): Response
    {
        $user = $this->security->getUser();
        $staffPosition = $staffPositionsRepository->findOneByCompanyID($user->getCompany(), $id);
        return $this->render('staff_positions/show.html.twig', [
            'staff_position' => $staffPosition,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="staff_positions_edit", methods={"GET","POST"})
     * @param Request $request
     * @param StaffPositionsRepository $staffPositionsRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, StaffPositionsRepository $staffPositionsRepository, $id): Response
    {
        $user = $this->security->getUser();
        $staffPosition = $staffPositionsRepository->findOneByCompanyID($user->getCompany(), $id);
        $form = $this->createForm(StaffPositionsType::class, $staffPosition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('staff_positions_index');
        }

        return $this->render('staff_positions/edit.html.twig', [
            'staff_position' => $staffPosition,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="staff_positions_delete", methods={"DELETE"})
     * @param Request $request
     * @param StaffPositions $staffPosition
     * @return Response
     */
    public function delete(Request $request, StaffPositions $staffPosition): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staffPosition->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staffPosition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('staff_positions_index');
    }
}
