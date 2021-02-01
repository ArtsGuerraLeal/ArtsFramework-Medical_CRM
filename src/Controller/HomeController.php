<?php

namespace App\Controller;

use App\Repository\SaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AppointmentRepository;
use App\Repository\ProductSoldRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @var SaleRepository
     */
    private $saleRepository;


    public function __construct(SaleRepository $saleRepository, AppointmentRepository $appointmentRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->appointmentRepository = $appointmentRepository;
        $this->saleRepository = $saleRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="index", methods={"GET"})
     * @param SaleRepository $saleRepository
     * @param ProductSoldRepository $productSoldRepository
     * @return Response
     */
    public function index(SaleRepository $saleRepository,ProductSoldRepository $productSoldRepository): Response
    {
        $user = $this->security->getUser();
        $date = new \DateTime();
        $yesterday = new \DateTime();
        $yesterday->sub(new \DateInterval('P1D'));
        $products = $productSoldRepository->GetMostSoldProducts($user->getCompany(),$date);
        $clients = $saleRepository->GetMostFrequentClientMonth($user->getCompany(),$date);
        $monthlySales = $saleRepository->GetSalesPerMonth($user->getCompany(),$date);

       //  foreach ($products as $key) {
       //     var_dump($key);
       // }
       // if($user->getRoles())

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'totalSalesMonth' => $monthlySales,
            'clients' => $clients,
            'products' => $products,
            'sales' => $saleRepository->findAllByCompanyDate($user->getCompany(),$date),
            'salesmonth' => $saleRepository->findAllByCompanyMonth($user->getCompany(),$date),
            'salesyesterday' => $saleRepository->findAllByCompanyDate($user->getCompany(),$yesterday),

        ]);

    }

    /**
     * @Route("/sales", name="sales_dashboard", methods={"GET"})
     * @param SaleRepository $saleRepository
     * @return Response
     */
    public function SaleDashboard(SaleRepository $saleRepository): Response
    {
        $user = $this->security->getUser();

        return $this->render('home/SalesDashboard.html.twig', [
            'controller_name' => 'HomeController',
            'sales' => $saleRepository->findByCompany($user->getCompany()),
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
