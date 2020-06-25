<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CompanySettingsController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/settings/company", name="company_settings", methods={"GET","POST"})
     * @param CompanyRepository $companyRepository
     * @param Request $request
     * @return Response
     */
    public function index(CompanyRepository $companyRepository, Request $request)
    {
        //TODO:Add dropdown to choose form to fill
        //TODO:Add export to pdf functionality

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);


        $dataForm = $this->createFormBuilder();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);


        $dataForm->add('setting1', TextType::class);
        $dataForm->add('setting2', TextType::class);
        $dataForm->add('setting3', TextType::class);
        $dataForm->add('setting4', TextType::class);
        $dataForm->add('setting5', TextType::class);
        $dataForm->add('setting6', TextType::class);

        $dataForm ->add('save', SubmitType::class, ['label' => 'Save']);

        $form = $dataForm->getForm();
        $form->handleRequest($request);
        $jsonContent = $serializer->serialize($form->getData(), 'json');

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $company->setSettings(array($jsonContent));
                $entityManager->persist($user);
                $entityManager->flush();

            }
            catch(IOException $e) {
            }

            return $this->redirectToRoute('company_settings');
        }


        return $this->render('company_settings/index.html.twig', [
            'controller_name' => 'CompanySettingsController',
            'form' => $form->createView(),
            'company' => $company,
        ]);
    }

}
