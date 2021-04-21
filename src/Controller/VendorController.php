<?php

namespace App\Controller;

use App\Entity\Vendor;
use App\Form\VendorType;
use App\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vendor")
 */
class VendorController extends AbstractController
{
    /**
     * @Route("/", name="vendor_index", methods={"GET"})
     */
    public function index(VendorRepository $vendorRepository): Response
    {
        return $this->render('vendor/index.html.twig', [
            'vendors' => $vendorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="vendor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vendor = new Vendor();
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vendor);
            $entityManager->flush();

            return $this->redirectToRoute('vendor_index');
        }

        return $this->render('vendor/new.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendor_show", methods={"GET"})
     */
    public function show(Vendor $vendor): Response
    {
        return $this->render('vendor/show.html.twig', [
            'vendor' => $vendor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vendor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vendor $vendor): Response
    {
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vendor_index');
        }

        return $this->render('vendor/edit.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendor_delete", methods={"POST"})
     */
    public function delete(Request $request, Vendor $vendor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vendor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vendor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vendor_index');
    }
}
