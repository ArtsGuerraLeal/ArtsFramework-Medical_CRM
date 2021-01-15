<?php

namespace App\Controller;

use App\Entity\Discount;
use App\Form\DiscountType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/discount")
*/
class DiscountController extends AbstractController
{
    
    /**
     * @Route("/", name="discount_index", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('discount/index.html.twig', [
            'controller_name' => 'DiscountController',
        ]);
    }

    /**
     * @Route("/new", name="discount_new", methods={"GET","POST"})
     * @return Response
     */
    public function new(Request $request): Response
    {
        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
           
           $entityManager->persist($discount);
           $entityManager->flush();

            return $this->redirectToRoute('discount_index');
        }

        return $this->render('discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form->createView(),
        ]);
    }
}
