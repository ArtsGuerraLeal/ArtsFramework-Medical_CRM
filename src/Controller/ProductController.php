<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductUploadType;
use App\Repository\ProductRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
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
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**@var UploadedFile $file */
            $file = $request->files->get('product')['attachment'];
            if($file){
                $filename = md5(uniqid()). '.' . $file->guessClientExtension();
                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename);
                $product->setImage($filename);
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
        $form = $this->createForm(ProductUploadType::class);
        $form->handleRequest($request);

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
                                if($s[0]=="'"){
                                    $s = ltrim($s, $s[0]);
                                }
                                $product->setSku($s);
                            }
                            if($c==16){
                                $product->setQuantity(intval($data[$c]));
                            }
                            if($c==19){
                                $product->setPrice(floatval($data[$c]));
                            }
                            if($c==46){
                                $product->setCost(floatval($data[$c]));
                            }
                          //  $product->setCompany(3);

                        }
                        $entityManager->persist($product);
                        $entityManager->flush();
                    
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
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);

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
}
