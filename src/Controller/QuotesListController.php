<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Repository\QuoteRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quotes")
 */
class QuotesListController extends AbstractController
{

    private $security;
  
    private $session;


    public function __construct(Security $security,SessionInterface $session){
      
        $this->security = $security;
        
        $this->session = $session;

    }

    
    /**
     * @Route("/", name="quotes_list")
     */
    public function index(QuoteRepository $quoteRepository)
    {
        $user = $this->security->getUser();

        return $this->render('quotes_list/index.html.twig', [
            'quotes' => $quoteRepository->findByCompany($user->getCompany()),
        ]);
    }


    /**
     * @Route("/{id}", name="quotes_list_show", methods={"GET"})
     * @param Quote $quote
     * @return Response
     */
    public function show(Quote $quote): Response
    {
        return $this->render('quotes_list/show.html.twig', [
            'quote' => $quote,
        ]);
    }
}
