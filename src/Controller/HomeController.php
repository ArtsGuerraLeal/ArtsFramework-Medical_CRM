<?php

namespace App\Controller;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/dashboard", name="home.")
 */
class HomeController extends AbstractController
{



    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $security;


    public function __construct(AppointmentRepository $appointmentRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->appointmentRepository = $appointmentRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param AppointmentRepository $appointmentRepository
     * @return Response
     */
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->security->getUser();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'appointments' => $appointmentRepository->findByCompany($user->getCompany()),
        ]);


    }

    /**
     * @Route("/test", name="architect", methods={"GET"})
     * @param AppointmentRepository $appointmentRepository
     * @return Response
     */
    public function architect(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->security->getUser();

        return $this->render('base_architect.html.twig', [
            'controller_name' => 'HomeController',
            'appointments' => $appointmentRepository->findByCompany($user->getCompany()),
        ]);


    }
}
