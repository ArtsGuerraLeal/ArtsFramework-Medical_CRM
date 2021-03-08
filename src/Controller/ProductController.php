<?php

namespace App\Controller;

use Aws\S3\S3Client;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductStock;
use App\Form\ProductUploadType;
use App\Repository\ProductRepository;
use App\Repository\ProductSoldRepository;
use App\Repository\ProductStockRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    private $security;
  
    private $session;

     /**
     * @var ProductSoldRepository
     */
    private $productSoldRepository;

    /**
     * @var ProductStockRepository
     */
    private $productStockRepository;


    /**
     * @var ProductRepository
     */
    private $productRepository;


    public function __construct(ProductRepository $productRepository,ProductStockRepository $productStockRepository, Security $security,SessionInterface $session, ProductSoldRepository $productSoldRepository){
      
        $this->security = $security;
        
        $this->session = $session;
        
        $this->productSoldRepository = $productSoldRepository;

        $this->productStockRepository = $productStockRepository;

        $this->productRepository = $productRepository;

    }

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findOneByCompany($user->getCompany())
        ]);
    }

    /**
     * @Route("/inventory", name="inventory_adjustment", methods={"GET"})
     */
    public function inventoryAdjustment(ProductRepository $productRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('product/inventory_adjustment.html.twig', [
            'products' => $productRepository->findOneByCompany($user->getCompany())
        ]);
    }

    /**
     * @Route("/print", name="product_print", methods={"GET"})
     */
    public function printProducts(ProductRepository $productRepository): Response
    {
        return $this->render('product/products_print.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->security->getUser();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $config = parse_ini_file('../AmazonConfig.ini');
        $skey = $config['amazon_secret_key'];
        $key = $config['amazon_key'];

        $s3 = new S3Client([
            'region'  => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $key,
                'secret' => $skey,
            ]
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**@var UploadedFile $file */
            $file = $request->files->get('product')['attachment'];
            if($file){
                $filename = md5(uniqid()). '.' . $file->guessClientExtension();

                $temp_file_location = $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename);

                $product->setImage($filename);


                $s3->putObject([
                    'Bucket' => 'pos.artstech',
                    'Key'    => $user->getCompany()->getName().'/'.$filename,
                    'SourceFile' => $temp_file_location,
                    'ACL'    => 'public-read'
                ]);
                    
                unlink($temp_file_location);

               // $file->move($this->getParameter('uploads_dir'),$filename);

                
            }
           $product->setCompany($user->getCompany());

           if($product->getQuantity() != null){
            $stock = new ProductStock();
            $stock->setCompany($user->getCompany());
            $stock->setAmount($product->getQuantity());
            $stock->setProduct($product);
            $stock->setTime(new \DateTime());
            $entityManager->persist($stock);
        }

           $entityManager->persist($product);
           $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/upload", name="product_upload", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        $user = $this->security->getUser();
        $form = $this->createForm(ProductUploadType::class);
        $form->handleRequest($request);

        $config = parse_ini_file('../AmazonConfig.ini');
        $skey = $config['amazon_secret_key'];
        $key = $config['amazon_key'];

        $s3 = new S3Client([
            'region'  => 'us-east-1',
            'version' => 'latest',
            'credentials' => [
                'key'    => $key,
                'secret' => $skey,
            ]
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**@var UploadedFile $file */
            $file = $request->files->get('product_upload')['attachment'];
            if($file){
                
                if (($fp = fopen($file, "r")) !== FALSE) {
                    $flag = true;
                    while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
                        if($flag) { $flag = false; continue; }
                        
                       
                        $product = new Product();
                        
                        $num = count($data);
                       
                        for ($c=0; $c < $num; $c++) {
                            //3 Vendor. Look for one and if it doesnt exist, create one
                            //4 Category. Look for one and if it doesnt exist, create one.
                            if($c==0){
                                $product->setUpc($data[$c]);
                            }
                            if($c==1){
                                $product->setName($data[$c]);
                            }
                            if($c==13){
                                $s = $data[$c];
                               
                                if( isset($s[0]) ){
                                    if($s[0]=="'"){
                                        $s = ltrim($s, $s[0]);
                                    }
                                    $product->setSku($s);
                                }
                             
                            }
                            if($c==16){
                                $product->setQuantity(intval($data[$c]));
                            }
                            if($c==19){
                                $product->setPrice(floatval($data[$c]));
                            }

                            if($c==24){
                            $product->setImage($data[$c]);

                                if($product->getImage() != ''){
                                    $image = file_get_contents($data[$c]);
                                    $uID = md5(uniqid());

                                    $file = file_put_contents($this->getParameter('uploads_dir').$uID.'.png', $image);
                                    
                                    $filename = $uID. '.png';
                                    $product->setImage($filename);
                               
                                    $s3->putObject([
                                        'Bucket' => 'pos.artstech',
                                        'Key'    => $user->getCompany()->getName().'/'.$filename,
                                        'SourceFile' => $this->getParameter('uploads_dir').$filename,
                                        'ACL'    => 'public-read'
                                    ]);

                                   unlink($this->getParameter('uploads_dir').$filename);

                                }else{

                                    $product->setImage(null);
                                }
                

                            }
                            if($c==46){
                                $product->setCost(floatval($data[$c]));
                            }
                            $product->setCompany($user->getCompany());

                        }
                        
                        if($product->getSku()!=null){
                            $entityManager->persist($product);
                            $entityManager->flush();
                        }
                        
                        
                      }
                      fclose($fp);
                }
            }
           
        //   $entityManager->persist($product);

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/upload.html.twig', [
    
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        $user = $this->security->getUser();
        $productSales = $this->productSoldRepository->FindSalesWithProduct($user->getCompany(),$product->getId());
        $productStocks = $this->productStockRepository->FindStockChangedOfProduct($user->getCompany(),$product->getId());

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'sales' => $productSales,
            'stocks' => $productStocks
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $quantity = $product->getQuantity();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**@var UploadedFile $file */
            $file = $request->files->get('product')['attachment'];
                if($file){
                    if($product->getImage() != null){
                        $filename = $product->getImage();
                        $file->move(
                            $this->getParameter('uploads_dir'),
                            $filename);
                        $product->setImage($filename);

                    }else{
                        $filename = md5(uniqid()). '.' . $file->guessClientExtension();
                        $file->move(
                            $this->getParameter('uploads_dir'),
                            $filename);
                        $product->setImage($filename);
                    }
                
                }
                $newQuantity = $request->request->get('product')['quantity'];
               if($quantity != $newQuantity){
                $stock = new ProductStock();
                $stock->setCompany($user->getCompany());

                if($quantity < $newQuantity){
                    $stock->setAmount($newQuantity - $quantity);

                }else{
                    $stock->setAmount(($quantity - $newQuantity)/-1);
                }
                $stock->setProduct($product);
                $stock->setTime(new \DateTime());
                $entityManager->persist($stock);
                $entityManager->flush();
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }


    /**
     * @Route("/adjust", name="adjust_inventory_post", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function adjustInventory(Request $request):JsonResponse
    {
        $user = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
            $productIDs = $request->request->get('productIDs');
            $quantities = $request->request->get('newInventory');
           

        }
        else {
            die();
        }


        $em = $this->getDoctrine()->getManager();

        $count = 0;

        foreach ($productIDs as $productId){
            $product = $this->productRepository->findOneBy(['id'=>$productId]);
            if($quantities[$count] != $product->getQuantity()){
                $quantity = $product->getQuantity();
                $product->setQuantity($quantities[$count]);

                
                $newQuantity = $quantities[$count];

                $stock = new ProductStock();
                $stock->setCompany($user->getCompany());

                if($quantity < $newQuantity){
                    $stock->setAmount($newQuantity - $quantity);

                }else{
                    $stock->setAmount(($quantity - $newQuantity)/-1);
                }
                $stock->setProduct($product);
                $stock->setTime(new \DateTime());

                $em->persist($stock);
                $em->persist($product);
                
            }

            $count++;

        }
        
        $em->flush();


        $response = '1';

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }
}
