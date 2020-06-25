<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\ReportPatientType;
use App\Repository\PatientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class ReportController extends AbstractController
{
    /**
     * @var PatientRepository
     */
    private $patientRepository;

    private $security;

    public function __construct(PatientRepository $patientRepository, Security $security)
    {
        $this->patientRepository = $patientRepository;
        $this->security = $security;
    }

    /**
     * @Route("/report", name="report_index")
     */
    public function index()
    {
        return $this->render('report/index.html.twig', [
            'controller_name' => 'ReportController',
        ]);
    }

    /**
     * @Route("/report/patient", name="report_patient")
     * @param Request $request
     * @return Response
     */
    public function patientReport(Request $request): Response
    {
        $user = $this->security->getUser();
        $reportData = null;
        $entityManager = $this->getDoctrine()->getManager();


        $form = $this->createForm(ReportPatientType::class);
        $form->handleRequest($request);

        $formData = $form->getData();


        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('clear')->isClicked()){
                return $this->render('report/_patient.html.twig', [
                    'form' => $form->createView(),
                    'reports' => null
                ]);
            }
                $reportData = $this->patientRepository->createQueryBuilder('p')
                ->where('p.birthdate >= :firstDate AND p.birthdate <= :secondDate')
                ->andWhere('p.company = :companyId')
                ->setParameter('firstDate',$formData['startdate'])
                ->setParameter('secondDate',$formData['enddate'])
                ->setParameter('companyId',$user->getCompany()->getId())
                ->getQuery()
                ->getResult()
            ;





            return $this->render('report/_patient.html.twig', [
                'form' => $form->createView(),
                'reports' => $reportData
            ]);


        }

        return $this->render('report/_patient.html.twig', [
            'form' => $form->createView(),
            'reports' => $reportData
        ]);
    }
}
