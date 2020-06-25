<?php

namespace App\Controller;

use App\Entity\CustomData;
use App\Form\CustomDataType;
use App\Repository\CustomDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/customdata")
 */
class CustomDataController extends AbstractController
{

    /**
     * @var CustomDataRepository
     */
    private $customDataRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $security;


    public function __construct(CustomDataRepository $customDataRepository , EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->customDataRepository = $customDataRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="custom_data_index", methods={"GET"})
     */
    public function index(CustomDataRepository $customDataRepository): Response
    {
        return $this->render('custom_data/index.html.twig', [
            'custom_datas' => $customDataRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="custom_data_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $customData = new CustomData();
        $user = $this->security->getUser();
        $customData->setCompany($user->getCompany());

        $form = $this->createForm(CustomDataType::class, $customData);

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $form->handleRequest($request);

        $jsonContent = $serializer->serialize($form->getData(), 'json', ['ignored_attributes' => ['company','customForm']]);;

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customData);
            $entityManager->flush();

          //  return $this->redirectToRoute('custom_data_index');
        }

        return $this->render('custom_data/new.html.twig', [
            'custom_data' => $customData,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="custom_data_show", methods={"GET"})
     */
    public function show(CustomData $customDatum): Response
    {
        return $this->render('custom_data/show.html.twig', [
            'custom_datum' => $customDatum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="custom_data_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CustomData $customDatum): Response
    {
        $form = $this->createForm(CustomDataType::class, $customDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('custom_data_index');
        }

        return $this->render('custom_data/edit.html.twig', [
            'custom_datum' => $customDatum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="custom_data_delete", methods={"DELETE"})
     */
    public function delete(Request $request, CustomData $customDatum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customDatum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customDatum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('custom_data_index');
    }
}
