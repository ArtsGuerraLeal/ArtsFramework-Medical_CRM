<?php

namespace App\Controller;

use App\Entity\State;
use App\Form\StateType;
use App\Repository\StateRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;



/**
 * @Route("/state")
 */
class StateController extends AbstractFOSRestController
{

    /**
     * @var StateRepository
     */
    private $stateRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(StateRepository $stateRepository , EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        $this->stateRepository = $stateRepository;
    }
    /**
     * @Route("/", name="state_index", methods={"GET"})
     * @param StateRepository $stateRepository
     * @return Response
     */
    public function index(StateRepository $stateRepository): Response
    {
        return $this->render('state/index.html.twig', [
            'states' => $stateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="state_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $state = new State();
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            /**@var UploadedFile $file */
            $file = $request->files->get('state')['attachment'];
            if($file){
                $filename = md5(uniqid()). '.' . $file->guessClientExtension();
                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename);
                $state->setImage($filename);
                $entityManager->persist($state);
                $entityManager->flush();
            }


            return $this->redirectToRoute('state_index');
        }

        return $this->render('state/new.html.twig', [
            'state' => $state,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="state_show", methods={"GET"})
     * @param State $state
     * @return Response
     */
    public function show(State $state): Response
    {
        return $this->render('state/show.html.twig', [
            'state' => $state,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="state_edit", methods={"GET","POST"})
     * @param Request $request
     * @param State $state
     * @return Response
     */
    public function edit(Request $request, State $state): Response
    {
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('state_index');
        }

        return $this->render('state/edit.html.twig', [
            'state' => $state,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="state_delete", methods={"DELETE"})
     * @param Request $request
     * @param State $state
     * @return Response
     */
    public function delete(Request $request, State $state): Response
    {
        if ($this->isCsrfTokenValid('delete'.$state->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($state);
            $entityManager->flush();
        }

        return $this->redirectToRoute('state_index');
    }

    public function getStateActions(State $state){
        return $this->view($state,Response::HTTP_OK);
    }

    public function getStatesActions(){
        $data = $this->stateRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="name", description="State name", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postStateAction(ParamFetcher $paramFetcher){
        $name = $paramFetcher->get('name');
        if($name){
            $state = new State();
            $state->setName($name);
            $this->entityManager->persist($state);
            $this->entityManager->flush();
            return $this->view($state,Response::HTTP_CREATED);
        }
        return $this->view(['name' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }


}
