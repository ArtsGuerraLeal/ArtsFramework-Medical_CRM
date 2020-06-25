<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\State;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/address")
 */
class AddressController extends AbstractFOSRestController
{
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var StateRepository
     */
    private $stateRepository;

    private $security;

    public function __construct(AddressRepository $addressRepository ,StateRepository $stateRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->addressRepository = $addressRepository;
        $this->stateRepository = $stateRepository;
        $this->security = $security;
    }

    /**
     * @Route("/", name="address_index", methods={"GET"})
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function index(AddressRepository $addressRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/new", name="address_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $address = new Address();
        $user = $this->security->getUser();
        $address->setCompany($user->getCompany());
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('address_index');
        }

        return $this->render('address/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="address_show", methods={"GET"})
     * @param Address $address
     * @return Response
     */
    public function show(Address $address): Response
    {
        return $this->render('address/show.html.twig', [
            'address' => $address,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="address_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Address $address
     * @return Response
     */
    public function edit(Request $request, Address $address): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('address_index');
        }

        return $this->render('address/edit.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="address_delete", methods={"DELETE"})
     * @param Request $request
     * @param Address $address
     * @return Response
     */
    public function delete(Request $request, Address $address): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($address);
            $entityManager->flush();
        }

        return $this->redirectToRoute('address_index');
    }

    public function getAddressActions(Address $address){
        return $this->view($address,Response::HTTP_OK);
    }

    public function getAddressesActions(){
        $data = $this->addressRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    public function getAddressStateActions(State $state){
        return $this->view($state,Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="line1", description="Line 1", nullable=false)
     * @Rest\RequestParam(name="line2", description="Line 2", nullable=false)
     * @Rest\RequestParam(name="postalCode", description="Postal Code", nullable=false)
     * @Rest\RequestParam(name="city", description="City", nullable=false)
     * @Rest\RequestParam(name="stateId", description="State", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postAddressAction(ParamFetcher $paramFetcher){
        $line1 = $paramFetcher->get('line1');
        $line2 = $paramFetcher->get('line2');
        $postal_code = $paramFetcher->get('postalCode');
        $city = $paramFetcher->get('city');
        $stateId = $paramFetcher->get('stateId');
        $state = $this->stateRepository->findOneBy(['id' => $stateId]);

        if($state){
                $address = new Address();
                $address->setLine1($line1);
                $address->setLine2($line2);
                $address->setPostalCode($postal_code);
                $address->setCity($city);
                $address->setState($state);
                $this->entityManager->persist($address);
                $this->entityManager->flush();
                return $this->view($address,Response::HTTP_CREATED);

        }
        return $this->view(['state' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }

}
