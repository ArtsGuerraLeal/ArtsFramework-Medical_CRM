<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\ProviderOrder;
use App\Entity\ProductOrdered;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProviderOrderRepository;
use App\Repository\ProductOrderedRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Qipsius\TCPDFBundle\Controller\TCPDFController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
* @Route("/order")
*/
class OrderController extends AbstractController
{
    
    private $entityManager;
    protected TCPDFController $tcpdf;
    private $security;

    public function __construct(EntityManagerInterface $entityManager,Security $security,TCPDFController $tcpdf){
        $this->entityManager = $entityManager;
        $this->tcpdf = $tcpdf;
        $this->security = $security;

    }
    /**
     * @Route("/", name="order_list")
     */
    public function index(ProviderOrderRepository $providerOrderRepository): Response
    {

        $user = $this->security->getUser();


        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'orders' => $providerOrderRepository->findByCompany($user->getCompany()),
        ]);
    }


    /**
     * @Route("/{id}", name="order_show", methods={"GET"})
     * @param ProviderOrder $order
     * @return Response
     */
    public function show(ProviderOrder $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}/compose", name="order_compose", methods={"GET"})
     * @param ProviderOrder $order
     * @return Response
     */
    public function compose(ProviderOrder $order,ProviderOrderRepository $providerOrderRepository, $id): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

      
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
        $html = $this->renderView('order/purchase_order.html.twig', [
            'title' => "Zampree",
            'note' => '',
            'order' => $order
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        
      //  $file->move($this->getParameter('uploads_dir'),$filename);

        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);


        return $this->render('order/compose.html.twig', [
            'order' => $order,
            'oids' => ''
        ]);
    }

    /**
     * @Route("/{id}/edit", name="order_edit", methods={"GET","POST"})
     * @param ProviderOrder $order
     * @return Response
     */
    public function edit(ProviderOrder $order,ProductRepository $productRepository): Response
    {
        $user = $this->security->getUser();
        $products = $productRepository->findOneByCompany($user->getCompany());
        $entityManager = $this->getDoctrine()->getManager();


        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'products' => $products,
        ]);
    }

    /**
     * @Route("/{id}/pdf", name="order_pdf", methods={"GET","POST"})
     * @param ProviderOrderRepository $providerOrderRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdf(ProviderOrderRepository $providerOrderRepository, $id, Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

        
      
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
        $html = $this->renderView('order/purchase_order.html.twig', [
            'title' => "Order Zampree",
            'order' => $order,
            'note' => ''
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $output = $dompdf->output();
        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        
    //    $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);

      //  $file->move($this->getParameter('uploads_dir'),$filename);

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($order->getId()."_report.pdf", [
            //Show download box on open
            "Attachment" => false
        ]);

    }

    /**
     * @Route("/editamount", name="order_edit_post", methods={"POST"})
     * @param ProviderOrderRepository $providerOrderRepository
     * @param ProductOrderedRepository $product
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function EditOrder(ProviderOrderRepository $providerOrderRepository, ProductOrderedRepository $productOrderedRepository, Request $request):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            
            //Order ID
            $id = $request->request->get('id');
            //Product Ids
            $products = $request->request->get('products');
            //Product Quantites
            $quantity = $request->request->get('quantity');
            //Get note
            $note = $request->request->get('note');
            $line1 = $request->request->get('line1');
            $line2 = $request->request->get('line2');
            $city = $request->request->get('city');
            $state = $request->request->get('state');
            $postalcode = $request->request->get('postalcode');
            $telephone = $request->request->get('telephone');

        }
        else {
            die();
        }

        $user = $this->security->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

        $productsOrdered = $order->getProductOrdereds();

        $count = 0;
        $newTotal = 0;
        foreach ($productsOrdered as $prod ){
            $cost = $prod->getProduct()->getCost();
            $newTotal = $newTotal + ($cost * $quantity[$count]);

            $prod->setAmount($quantity[$count]);
            $entityManager->persist($prod);
            $entityManager->flush();
            $count++;
        }
        $order->setLine1($line1);
        $order->setLine2($line2);
        $order->setCity($city);
        $order->setState($state);
        $order->setPostalcode($postalcode);
        $order->setTelephone($telephone);
        $order->setNote($note);
        $order->setTotal($newTotal);

        $entityManager->persist($order);
        $entityManager->flush();

    


  $returnResponse = new JsonResponse();
  $returnResponse->setjson(200);

  return $returnResponse;

       
    }

    /**
     * @Route("/compose/post", name="order_pdf_compose", methods={"POST"})
     * @param ProviderOrderRepository $order
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function ComposePDF(ProviderOrderRepository $providerOrderRepository,Request $request,\Swift_Mailer $mailer):JsonResponse
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
            if($oids != ''){
                $this->createMultiplePDF($providerOrderRepository,$note,$oids);
            }else{
                $this->createSinglePDF($providerOrderRepository,$note,$id);
            }
        }

        $order = $providerOrderRepository->findOneByCompanyID($usr->getCompany(), $id);

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
            ->attach(\Swift_Attachment::fromPath($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf"))
            ;

            $mailer->send($message);
            
            unlink($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf");


  $returnResponse = new JsonResponse();
  $returnResponse->setjson(200);

  return $returnResponse;
    }

    /**
     * @Route("/pdf/send", name="order_pdf_send", methods={"POST"})
     * @param ProviderOrderRepository $order
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function SendPdf(ProviderOrderRepository $providerOrderRepository,Request $request,\Swift_Mailer $mailer):JsonResponse
    {

        if ($request->getMethod() == 'POST')
        {
            $user = $this->security->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), 6);
        }
        else {
            die();
        }

        $config = parse_ini_file('../MailConfig.ini');

        $user = $config['user'];
        $pass = $config['passwd'];
        $server = $config['server'];
        $port = $config['port'];

        //set for gmail
        //  $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 465,'ssl'))
        //  ->setUsername('')
        // ->setPassword('');

        //set for other

        $transport = (new \Swift_SmtpTransport($server, 465,'ssl'))
        ->setUsername($user)
        ->setPassword($pass);
    

            $mailer = new \Swift_Mailer($transport);

            $message = (new \Swift_Message('Wonderful Subject'))
            ->setFrom(['john@doe.com' => 'Andres Zambrano'])
            ->setTo(['guerraarturo11@gmail.com', 'guerraarturo@icloud.com' => 'A name'])
            ->setBody('Mensaje')
            ->attach(\Swift_Attachment::fromPath($this->getParameter('temp_storage_dir').$user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf"))
            ;

            $mailer->send($message);
        

    


  $returnResponse = new JsonResponse();
  $returnResponse->setjson(6);

  return $returnResponse;

    }

    /**
     * @Route("/compose/separate/post", name="order_pdf_compose_separate", methods={"POST"})
     * @param ProviderOrderRepository $order
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function ComposePDFSeparate(ProviderOrderRepository $providerOrderRepository,Request $request,\Swift_Mailer $mailer):JsonResponse
    {
        $usr = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
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

        $orderArray = explode(",",$oids);

        if($note != ''){
            foreach ($orderArray as $id) {
                $this->createSinglePDF($providerOrderRepository,$note,$id);
            }
            
        }

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
            ->setBody($body);

            foreach ($orderArray as $id) {
                $order = $providerOrderRepository->findOneByCompanyID($usr->getCompany(), $id);
                $message->attach(\Swift_Attachment::fromPath($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf"));

            }

            $mailer->send($message);
            
            unlink($this->getParameter('temp_storage_dir').$usr->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf");


            $returnResponse = new JsonResponse();
            $returnResponse->setjson(200);

            return $returnResponse;
    }

    /**
     * @Route("/pdf/multiple", name="order_pdf_preview_multiple", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdfPreviewMultiple(ProviderOrderRepository $providerOrderRepository, Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
       
        //Order IDs
        $oids = $_GET['oids'];

        
        $orderArray = explode(",",$oids);
       
        $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;

        foreach($orders as $ord){
        
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $ord);
        
        }
       
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
        $html = $this->renderView('order/purchase_order_multiple.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'orders' => $orders,
            'note' =>''
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($order->getId()."_report.pdf", [
            //Show download box on open
            "Attachment" => false
        ]);

    }

    public function createSinglePDF($providerOrderRepository,$note,$id)
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
    
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);
    

     

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
        $html = $this->renderView('order/purchase_order.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'note' => $note
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        
        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);

    }


    public function createMultiplePDF($providerOrderRepository,$note, $oids)
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
        

        if($oids != null){
            $orderArray = explode(",",$oids);
            $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;
            $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $orders[0]);
    

        }
        

        //foreach($orders as $ord){
        //$order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $ord);
        //}

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
        $html = $this->renderView('order/purchase_order_multiple.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'orders' => $orders,
            'note' => $note
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        
        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);

    }

    /**
     * @Route("/pdf/multiple/compose/separate", name="order_pdf_compose_multiple_separate", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function ComposeMultiplePdfSeparate(ProviderOrderRepository $providerOrderRepository): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
        $oids = $_GET['oids'];

        
        $orderArray = explode(",",$oids);
       
        $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;

        foreach($orderArray as $id){
            $this->createSinglePDF($providerOrderRepository,'',$id);
        }

        return $this->render('order/compose_separate.html.twig', [
            'oidsArray' => $orderArray,
            'oids' => $oids,
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/pdf/multiple/compose/grouped", name="order_pdf_compose_multiple_grouped", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function ComposeMultiplePdfGrouped(ProviderOrderRepository $providerOrderRepository): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
        $oids = $_GET['oids'];

        
        $orderArray = explode(",",$oids);
        $groupedOrderArray = array();
        $groupedOrderArrayIDs = array();
        $singleOrderArray = array();
        $singleOrderArrayIDs = array();
        $orderIDs = array();
        $orderClients = array();

        $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;

        foreach($orders as $order){
            array_push($orderIDs,$order->getId());
            array_push($orderClients,$order->getClient());
            
        }

        $count = 0;

        foreach($orderClients as $client){

            if(array_count_values($orderClients)[$client] > 1){
                array_push($groupedOrderArray, $client);
                array_push($groupedOrderArrayIDs, $orderIDs[$count]);
                
            }
            if(array_count_values($orderClients)[$client] == 1){
                array_push($singleOrderArray, $client);
                array_push($singleOrderArrayIDs, $orderIDs[$count]);
                
            }
            $count++;
                
        }

      //  var_dump($groupedOrderArray);
      //  var_dump($groupedOrderArrayIDs);
      //  var_dump($singleOrderArray);
      //  var_dump($singleOrderArrayIDs);


     


       
        foreach($singleOrderArrayIDs as $id){
            $this->createSinglePDF($providerOrderRepository,'',$id);
        }

            $this->createGroupedPDF($providerOrderRepository,'',$groupedOrderArrayIDs);


        return $this->render('order/compose_grouped.html.twig', [
            'oidsArray' => $orderArray,
            'oids' => $oids,
            'orders' => $orders
        ]);
    }

   

    public function createGroupedPDF($providerOrderRepository,$note, $oids)
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
        

        
            $orderArray = $oids;
            $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;
            $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $orders[0]);
    

        
        

        //foreach($orders as $ord){
        //$order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $ord);
        //}

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
        $html = $this->renderView('order/purchase_order_multiple.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'orders' => $orders,
            'note' => $note
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        
        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);

    }

    /**
     * @Route("/pdf/multiple/compose", name="order_pdf_compose_multiple", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function ComposeMultiplePdf(ProviderOrderRepository $providerOrderRepository): Response
    {

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        //Order IDs
        $oids = $_GET['oids'];

        
        $orderArray = explode(",",$oids);
       
        $orders = $providerOrderRepository->findBy(['id' => $orderArray,'company'=>$user->getCompany()]);;

        //foreach($orders as $ord){
        //$order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $ord);
        //}
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $orders[0]);

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
        $html = $this->renderView('order/purchase_order_multiple.html.twig', [
            'title' => "Zampree",
            'order' => $order,
            'orders' => $orders,
            'note' => ''
        
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $filename = $user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf";
        

        $file = file_put_contents($this->getParameter('temp_storage_dir').$filename, $output);


        return $this->render('order/compose.html.twig', [
            'order' => $order,
            'oids' => $oids
            
        ]);
    }

     /**
     * @Route("/{id}/pdf/preview", name="order_pdf_preview", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdfPreview(ProviderOrderRepository $providerOrderRepository, $id, Request $request): Response
    {

       

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

      
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
        $html = $this->renderView('order/purchase_order.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'note' => ''
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf", [
            //Show download box on open
            "Attachment" => false
        ]);

    }

    /**
     * @Route("/{id}/pdf/download", name="order_pdf_download", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdfDownload(ProviderOrderRepository $providerOrderRepository, $id, Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

      
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
        $html = $this->renderView('order/purchase_order.html.twig', [
            'title' => "Welcome to our PDF Test",
            'order' => $order,
            'note'=> ''
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($user->getCompany()->getName().' - PO '.$order->getOrderNumber().".pdf", [
            //Show download box on open
            "Attachment" => true
        ]);

    }



    /**
     * @Route("/{id}/pdf2", name="order_pdf2", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdf2(ProviderOrderRepository $providerOrderRepository, $id, Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

      
        // Configure Dompdf according to your needs
    

        return $this->render('order/purchase_order.html.twig', [
            'order' => $order,
        ]);


    }

    /**
     * @Route("/{id}/pdf3", name="order_pdf3", methods={"GET","POST"})
     * @param ProviderOrderRepository $order
     * @param $id
     * @throws NonUniqueResultException
     */
    public function pdf3(ProviderOrderRepository $providerOrderRepository, $id): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $order = $providerOrderRepository->findOneByCompanyID($user->getCompany(), $id);

        
        $pdf = $this->tcpdf->create('vertical', 'mm', 'A4', false, 'UTF-8', false);
        $pdf->SetAuthor('Our Code World');
        $pdf->SetTitle(('Our Code World Title'));
        $pdf->SetSubject('Our Code World Subject');
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 11, '', true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        //$pdf->SetMargins(20,20,40, true);
        $pdf->AddPage();
        
        $filename = 'ourcodeworld_pdf_demo';
        

        $html = $this->render('order/purchase_order.html.twig', ['order' => $order])->getContent();
       // $html = file_get_contents('order/purchase_order.html.twig');

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $pdf->Output($filename.".pdf",'I');

        

        return $this->render('order/purchase_order_bs.html.twig', [
            'order' => $order,
        ]);


    }

  

}
