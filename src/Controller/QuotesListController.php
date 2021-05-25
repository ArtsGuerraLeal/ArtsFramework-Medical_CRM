<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Sale;
use App\Entity\Quote;
use App\Entity\ProductSold;
use App\Entity\ProductSoldDiscount;
use App\Repository\QuoteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/quotes")
 */
class QuotesListController extends AbstractController
{

    private $security;
  
    private $session;

    private TCPDFController $tcpdf;


    public function __construct(Security $security,SessionInterface $session,TCPDFController $tcpdf){
      
        $this->security = $security;
        
        $this->session = $session;
        $this->tcpdf = $tcpdf;

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

    /**
     * @Route("/{id}/compose", name="quote_compose", methods={"GET"})
     * @param Quote $quote
     * @return Response
     */
    public function compose(Quote $quote, QuoteRepository $quoteRepository, $id): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $quote = $quoteRepository->findByCompanyID($user->getCompany(), $id);

      
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf();
        $dompdf->setOptions($pdfOptions);
        $dompdf->set_base_path("/public/");
        $base = $this->renderView('base.html.twig');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quote/purchase_order.html.twig', [
            'title' => $user->getCompany()->getName(),
            'note' => '',
            'quote' => $quote
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - '.$quote->getId().".pdf";
        
      //  $file->move($this->getParameter('uploads_dir'),$filename);

        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);


        return $this->render('quote/compose.html.twig', [
            'quote' => $quote,
            'oids' => ''
        ]);
    }

    /**
     * @Route("/compose/post", name="quote_pdf_compose", methods={"POST"})
     * @param QuoteRepository $quoteRepository
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function ComposePDF(QuoteRepository $quoteRepository,Request $request,\Swift_Mailer $mailer):JsonResponse
    {
        $usr = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
            $id = $request->request->get('id');
            $sender = $request->request->get('sender');
            $recipient = $request->request->get('reciever');
            $subject = $request->request->get('subject');
            $body = $request->request->get('body');
            $note = $request->request->get('note');
            $oids = $request->request->get('oids');

        }
        else {
            die();
        }


    
            
        if($note != ''){
            $this->createSinglePDF($quoteRepository,$note,$id);
        
        }            
        

        $quote = $quoteRepository->findByCompanyID($usr->getCompany(), $id);

        $config = parse_ini_file('../MailConfig.ini');

        $user = $config['user'];
        $pass = $config['passwd'];
        $server = $config['server'];
        $port = $config['port'];
        $postmaster = $config['postmaster'];

        //set for gmail
        //  $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465,'ssl'))
        //  ->setUsername('')
        // ->setPassword('');

        //set for other

        $transport = (new \Swift_SmtpTransport($server,$port,'ssl'))
        ->setUsername($user)
        ->setPassword($pass);
    

            $mailer = new \Swift_Mailer($transport);

            $message = (new \Swift_Message($subject))
            ->setFrom([$postmaster => $usr->getFirstname() . ' ' . $usr->getLastname()])
            ->setReplyTo($sender)
            ->setTo([$recipient, $sender => 'Me'])
            ->setBody($body)
            ->attach(\Swift_Attachment::fromPath($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - '.$quote->getId().".pdf"))
            ;

            $mailer->send($message);
            
            unlink($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - '.$quote->getId().".pdf");


            $returnResponse = new JsonResponse();
            $returnResponse->setjson(200);

            return $returnResponse;
    }

    /**
     * @Route("/{id}/pdf/preview", name="quote_pdf_preview", methods={"GET","POST"})
     * @param QuoteRepository $quoteRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdfPreview(QuoteRepository $quoteRepository, $id, Request $request): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $quote = $quoteRepository->findByCompanyID($user->getCompany(), $id);

      
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        //$pdfOptions->setIsHtml5ParserEnabled(true);
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf();
        
        $dompdf->setOptions($pdfOptions);
        //$dompdf->set_base_path("/public/");
        //$base = $this->renderView('base.html.twig');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quote/purchase_order.html.twig', [
            'title' => "",
            'quote' => $quote,
            'note' => ''
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($user->getCompany()->getName().' - '.$quote->getId().".pdf", [
            //Show download box on open
            "Attachment" => false
        ]);

        

    }

    /**
     * @Route("/{id}/pdf/download", name="quote_pdf_download", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdfDownload(QuoteRepository $quoteRepository, $id, Request $request): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $quote = $quoteRepository->findByCompanyID($user->getCompany(), $id);

      
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf();
        $dompdf->setOptions($pdfOptions);
        $dompdf->set_base_path("/public/");
        $base = $this->renderView('base.html.twig');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quote/purchase_order.html.twig', [
            'title' => "",
            'quote' => $quote,
            'note' => ''
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($user->getCompany()->getName().' - '.$quote->getId().".pdf", [
            //Show download box on open
            "Attachment" => true
        ]);

    }

    public function createSinglePDF($providerOrderRepository,$note,$id)
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
    
        $quote = $providerOrderRepository->findByCompanyID($user->getCompany(), $id);
    

     

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf();
        $dompdf->setOptions($pdfOptions);
        $dompdf->set_base_path("/public/");
        $base = $this->renderView('base.html.twig');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('quote/purchase_order.html.twig', [
            'title' => "",
            'quote' => $quote,
            'note' => $note
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - '.$quote->getId().".pdf";
        
        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);

    }

    public function returnPDFResponseFromHTML($html){
        //set_time_limit(30); uncomment this line according to your needs
        // If you are not in a controller, retrieve of some way the service container and then retrieve it
        //$pdf = $this->container->get("white_october.tcpdf")->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //if you are in a controlller use :
        $pdf = $this->tcpdf->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAuthor('Our Code World');
        $pdf->SetTitle(('Our Code World Title'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        
        $filename = 'ourcodeworld_pdf_demo';
        
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I'); // This will output the PDF as a response directly
}

/**
     * @Route("/{id}/convert/sale", name="quote_convert_sale", methods={"GET","POST"})
     * @param QuoteRepository $quoteRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function QuoteConvertSale(QuoteRepository $quoteRepository, $id, Request $request): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $quote = $quoteRepository->findByCompanyID($user->getCompany(), $id);
        $productsQuoted = $quote->getProductQuotes();
        $em = $this->getDoctrine()->getManager();

        $sale = new Sale();

        $sale->setTotal($quote->getTotal());
        $sale->setSubtotal($quote->getSubtotal());
        $sale->setTax($quote->getTax());
        $sale->setCompany($this->security->getUser()->getCompany());
        $sale->setTime(new \DateTime());
        $sale->setClientId($quote->getClient());
        $sale->setClient($quote->getClient()->getName());
        
        foreach ($productsQuoted as $prod){
            $product = $prod->getProduct();
            $productSold = new ProductSold();
            
            if($product->getQuantity() != null){
                $product->setQuantity($product->getQuantity() - $prod->getAmount());
            }

            $productSold->setProduct($product);
            $productSold->setAmount($prod->getAmount());
            $productSold->setSale($sale);
            $productSold->setCompany($this->security->getUser()->getCompany());
            $productSold->setPrice($product->getPrice() * $prod->getAmount());

        $em->persist($product);
        $em->persist($productSold);
        

        

    }
        $em->persist($sale);
        $em->flush();
    return $this->redirectToRoute('unpaid_sale_edit',['id'=>$sale->getId()]);

}

}
