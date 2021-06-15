<?php

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/material")
 */
class MaterialController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/", name="material_index", methods={"GET"})
     */
    public function index(MaterialRepository $materialRepository): Response
    {
        return $this->render('material/index.html.twig', [
            'materials' => $materialRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="material_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $material->setCompany($this->security->getUser()->getCompany());
            $entityManager->persist($material);
            $entityManager->flush();

            return $this->redirectToRoute('material_index');
        }

        return $this->render('material/new.html.twig', [
            'material' => $material,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="material_show", methods={"GET"})
     */
    public function show(Material $material): Response
    {
        return $this->render('material/show.html.twig', [
            'material' => $material,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="material_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Material $material): Response
    {
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('material_index');
        }

        return $this->render('material/edit.html.twig', [
            'material' => $material,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="material_delete", methods={"POST"})
     */
    public function delete(Request $request, Material $material): Response
    {
        if ($this->isCsrfTokenValid('delete'.$material->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($material);
            $entityManager->flush();
        }

        return $this->redirectToRoute('material_index');
    }
}
