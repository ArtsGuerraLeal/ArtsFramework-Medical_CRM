<?php

namespace App\Controller;

use App\Entity\Ensamble;
use App\Form\EnsambleType;
use App\Repository\EnsambleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ensamble")
 */
class EnsambleController extends AbstractController
{
    /**
     * @Route("/", name="ensamble_index", methods={"GET"})
     */
    public function index(EnsambleRepository $ensambleRepository): Response
    {
        return $this->render('ensamble/index.html.twig', [
            'ensambles' => $ensambleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ensamble_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ensamble = new Ensamble();
        $form = $this->createForm(EnsambleType::class, $ensamble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ensamble);
            $entityManager->flush();

            return $this->redirectToRoute('ensamble_index');
        }

        return $this->render('ensamble/new.html.twig', [
            'ensamble' => $ensamble,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ensamble_show", methods={"GET"})
     */
    public function show(Ensamble $ensamble): Response
    {
        return $this->render('ensamble/show.html.twig', [
            'ensamble' => $ensamble,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ensamble_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ensamble $ensamble): Response
    {
        $form = $this->createForm(EnsambleType::class, $ensamble);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ensamble_index');
        }

        return $this->render('ensamble/edit.html.twig', [
            'ensamble' => $ensamble,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ensamble_delete", methods={"POST"})
     */
    public function delete(Request $request, Ensamble $ensamble): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ensamble->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ensamble);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ensamble_index');
    }
}
