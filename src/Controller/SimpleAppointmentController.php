<?php

namespace App\Controller;

use App\Entity\SimpleAppointment;
use App\Form\SimpleAppointmentType;
use App\Repository\SimpleAppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/simpleappointment")
 */
class SimpleAppointmentController extends AbstractController
{
    /**
     * @Route("/", name="simple_appointment_index", methods={"GET"})
     */
    public function index(SimpleAppointmentRepository $simpleAppointmentRepository): Response
    {
        return $this->render('simple_appointment/index.html.twig', [
            'simple_appointments' => $simpleAppointmentRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="simple_appointment_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $simpleAppointment = new SimpleAppointment();
        $form = $this->createForm(SimpleAppointmentType::class, $simpleAppointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $simpleAppointment->setDate(new \DateTime());

            $entityManager->persist($simpleAppointment);
            $entityManager->flush();

            return $this->redirectToRoute('simple_appointment_index');
        }

        return $this->render('simple_appointment/new.html.twig', [
            'simple_appointment' => $simpleAppointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="simple_appointment_show", methods={"GET"})
     */
    public function show(SimpleAppointment $simpleAppointment): Response
    {
        return $this->render('simple_appointment/show.html.twig', [
            'simple_appointment' => $simpleAppointment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="simple_appointment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SimpleAppointment $simpleAppointment): Response
    {
        $form = $this->createForm(SimpleAppointmentType::class, $simpleAppointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('simple_appointment_index');
        }

        return $this->render('simple_appointment/edit.html.twig', [
            'simple_appointment' => $simpleAppointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="simple_appointment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SimpleAppointment $simpleAppointment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$simpleAppointment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($simpleAppointment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('simple_appointment_index');
    }
}
