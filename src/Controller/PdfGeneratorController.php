<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PdfGeneratorController extends AbstractController
{
    /**
     * @Route("/pdf", name="pdf_generator")
     */
    public function index()
    {

        return $this->render('pdf_generator/index.html.twig', [
            'controller_name' => 'PdfGeneratorController',
        ]);
    }
}
