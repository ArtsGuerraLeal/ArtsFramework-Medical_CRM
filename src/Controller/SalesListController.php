<?php

namespace App\Controller;

use App\Entity\Sale;
use App\Repository\DiscountRepository;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sales")
 */
class SalesListController extends AbstractController
{
    /**
     * @Route("/", name="sales_list")
     */
    public function index(SaleRepository $saleRepository)
    {
        return $this->render('sales_list/index.html.twig', [
            'sales' => $saleRepository->findAll(),
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
        return $this->render('sales_list/discount.html.twig', [
            'sales' => $saleRepository->findAll(),
            'discounts' => $discountRepository->findAll()
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
