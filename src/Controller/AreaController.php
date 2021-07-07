<?php

namespace App\Controller;

use App\Entity\Area;
use App\Form\AreaType;
use App\Repository\AreaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/area")
 */
class AreaController extends AbstractController
{

    private $security;
    
    /**
      * @var EntityManagerInterface
      */
     private $entityManager;

     /**
     * @var AreaRepository
     */
    private $areaRepository;
 
 
     public function __construct(EntityManagerInterface $entityManager,Security $security, AreaRepository $areaRepository){
         $this->entityManager = $entityManager;
         $this->security = $security;
         $this->areaRepository = $areaRepository;
 
     }
     
    /**
     * @Route("/", name="area_index", methods={"GET"})
     */
    public function index(AreaRepository $areaRepository): Response
    {
        return $this->render('area/index.html.twig', [
            'areas' => $areaRepository->findAll(),
        ]);
    }

    

    /**
     * @Route("/new", name="area_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $area = new Area();
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $area->setCompany($this->security->getUser()->getCompany());
            $entityManager->persist($area);
            $entityManager->flush();

            return $this->redirectToRoute('area_index');
        }

        return $this->render('area/new.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="area_show", methods={"GET"})
     */
    public function show(Area $area): Response
    {
        return $this->render('area/show.html.twig', [
            'area' => $area,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="area_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Area $area): Response
    {
        $form = $this->createForm(AreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('area_index');
        }

        return $this->render('area/edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="area_delete", methods={"POST"})
     */
    public function delete(Request $request, Area $area): Response
    {
        if ($this->isCsrfTokenValid('delete'.$area->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($area);
            $entityManager->flush();
        }

        return $this->redirectToRoute('area_index');
    }

    
}
