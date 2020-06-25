<?php

namespace App\Controller;

use App\Entity\Staff;
use App\Form\StaffType;
use App\Repository\StaffRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/staff")
 */
class StaffController extends AbstractController
{

    private $security;
    /**
     * @var StaffRepository
     */
    private $staffRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(StaffRepository $staffRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->staffRepository = $staffRepository;

        $this->security = $security;

    }

    /**
     * @Route("/", name="staff_index", methods={"GET"})
     * @param StaffRepository $staffRepository
     * @return Response
     */
    public function index(StaffRepository $staffRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('staff/index.html.twig', [
            'staff' => $staffRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="staff_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->security->getUser();
        $staff = new Staff();
        $staff->setCompany($user->getCompany());

        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staff);
            $entityManager->flush();

            return $this->redirectToRoute('staff_index');
        }

        return $this->render('staff/new.html.twig', [
            'staff' => $staff,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="staff_show", methods={"GET"})
     * @param StaffRepository $staffRepository
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(StaffRepository $staffRepository,$id): Response
    {
        $user = $this->security->getUser();
        $staff = $staffRepository->findOneByCompanyID($user->getCompany(), $id);
        return $this->render('staff/show.html.twig', [
            'staff' => $staff,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="staff_edit", methods={"GET","POST"})
     * @param Request $request
     * @param StaffRepository $staffRepository
     * @param $id
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, StaffRepository $staffRepository,$id): Response
    {
        $user = $this->security->getUser();
        $staff = $staffRepository->findOneByCompanyID($user->getCompany(), $id);
        $form = $this->createForm(StaffType::class, $staff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('staff_index');
        }

        return $this->render('staff/edit.html.twig', [
            'staff' => $staff,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="staff_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Staff $staff): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staff->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staff);
            $entityManager->flush();
        }

        return $this->redirectToRoute('staff_index');
    }
}
