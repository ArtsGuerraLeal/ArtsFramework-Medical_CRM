<?php

namespace App\Controller;

use App\Entity\MaritalStatus;
use App\Form\MaritalStatusType;
use App\Repository\MaritalStatusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/maritalstatus")
 */
class MaritalStatusController extends AbstractController
{
    /**
     * @Route("/", name="marital_status_index", methods={"GET"})
     */
    public function index(MaritalStatusRepository $maritalStatusRepository): Response
    {
        return $this->render('marital_status/index.html.twig', [
            'marital_statuses' => $maritalStatusRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="marital_status_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $maritalStatus = new MaritalStatus();
        $form = $this->createForm(MaritalStatusType::class, $maritalStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($maritalStatus);
            $entityManager->flush();

            return $this->redirectToRoute('marital_status_index');
        }

        return $this->render('marital_status/new.html.twig', [
            'marital_status' => $maritalStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marital_status_show", methods={"GET"})
     */
    public function show(MaritalStatus $maritalStatus): Response
    {
        return $this->render('marital_status/show.html.twig', [
            'marital_status' => $maritalStatus,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="marital_status_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MaritalStatus $maritalStatus): Response
    {
        $form = $this->createForm(MaritalStatusType::class, $maritalStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('marital_status_index');
        }

        return $this->render('marital_status/edit.html.twig', [
            'marital_status' => $maritalStatus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marital_status_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MaritalStatus $maritalStatus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maritalStatus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($maritalStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('marital_status_index');
    }
}
