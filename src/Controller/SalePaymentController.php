<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SalePaymentController extends AbstractController
{
    /**
     * @Route("/sale/payment", name="sale_payment")
     */
    public function index()
    {
        return $this->render('sale_payment/index.html.twig', [
            'controller_name' => 'SalePaymentController',
        ]);
    }
}
