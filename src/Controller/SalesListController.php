<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\SaleRepository;
use App\Repository\DiscountRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/sales")
 */
class SalesListController extends AbstractController
{

    private $security;
  
    private $session;


    public function __construct(Security $security,SessionInterface $session){
      
        $this->security = $security;
        
        $this->session = $session;

    }

    /**
     * @Route("/", name="sales_list")
     */
    public function index(SaleRepository $saleRepository)
    {
        $user = $this->security->getUser();

        return $this->render('sales_list/index.html.twig', [
            'sales' => $saleRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/discounts", name="sales_discounts_list")
     * @param SaleRepository $saleRepository
     * @param DiscountRepository $discountRepository
     * @return Response
     */
    public function DiscountedSales(SaleRepository $saleRepository,DiscountRepository $discountRepository)
    {
        $user = $this->security->getUser();

        return $this->render('sales_list/discount.html.twig', [
            'sales' => $saleRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/daily", name="sales_daily_list")
     * @param SaleRepository $saleRepository
     * @return Response
     */
    public function DailySales(SaleRepository $saleRepository)
    {
        $user = $this->security->getUser();
        $date = new \DateTime();
   
        if(isset($_GET['ReportDate'])){

            $reportDate = $_GET['ReportDate'];
            $date = new \DateTime($reportDate);
        }


        return $this->render('sales_list/daily.html.twig', [
            'sales' => $saleRepository->findAllByCompanyDate($user->getCompany(),$date),
        ]);
    }


    /**
     * @Route("/{id}", name="sales_list_show", methods={"GET"})
     * @param Sale $sale
     * @return Response
     */
    public function show(Sale $sale): Response
    {
        return $this->render('sales_list/show.html.twig', [
            'sale' => $sale,
        ]);
    }
}
