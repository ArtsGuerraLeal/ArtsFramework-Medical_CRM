<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Appointment;
use App\Entity\Company;
use App\Entity\MaritalStatus;
use App\Entity\Patient;
use App\Form\PatientAppointmentType;
use App\Form\PatientType;
use App\Repository\AddressRepository;
use App\Repository\AppointmentRepository;
use App\Repository\CompanyRepository;
use App\Repository\CustomDataRepository;
use App\Repository\CustomFormRepository;
use App\Repository\MaritalStatusRepository;
use App\Repository\PatientRepository;
use App\Service\Client;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Dompdf\Options;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Google_Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Dompdf\Dompdf;

/**
 * @Route("/patient")
 */
class PatientController extends AbstractFOSRestController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PatientRepository
     */
    private $patientRepository;
    /**
     * @var AddressRepository
     */
    private $addressRepository;
    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;
    /**
     * @var AppointmentRepository
     */
    private $maritalStatusRepository;

    /**
     * @var CustomDataRepository
     */
    private $customDataRepository;

    /**
     * @var CustomFormRepository
     */
    private $customFormRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    private $security;

    public function __construct(CompanyRepository $companyRepository, CustomFormRepository $customFormRepository, PatientRepository $patientRepository, AddressRepository $addressRepository, MaritalStatusRepository $maritalStatusRepository,CustomDataRepository $customDataRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->patientRepository = $patientRepository;
        $this->addressRepository = $addressRepository;
        //$this->appointmentRepository = $appointmentRepository;
        $this->customDataRepository = $customDataRepository;
        $this->maritalStatusRepository = $maritalStatusRepository;
        $this->customFormRepository = $customFormRepository;
        $this->companyRepository = $companyRepository;
        $this->security = $security;

    }

    /**
     * @Route("/", name="patient_index", methods={"GET"})
     * @param PatientRepository $patientRepository
     * @return Response
     */
    public function index(PatientRepository $patientRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('patient/index.html.twig', [
            'patients' =>
                $patientRepository->findArrayByCompany($user->getCompany()),
              //  $patientRepository->findByCompanyID($user->getCompany(),57),
        ]);
    }

    /**
     * @Route("/new", name="patient_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $patient = new Patient();
        $address = new Address();

        $address->setPatient($patient);
        $patient->getAddress()->add($address);

        $user = $this->security->getUser();
        $patient->setCompany($user->getCompany());
        $form = $this->createForm(PatientType::class, $patient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /**@var UploadedFile $file */
            $file = $request->files->get('patient')['attachment'];
            if($file){
                $filename = md5(uniqid()). '.' . $file->guessClientExtension();
                $file->move(
                    $this->getParameter('uploads_dir'),
                    $filename);
                $patient->setImage($filename);
            }
            $entityManager->persist($patient);
            $entityManager->flush();
            return $this->redirectToRoute('patient_index');
        }

        return $this->render('patient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/appointment", name="patient_appointment", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     * @throws Google_Exception
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function appointment(PatientRepository $patientRepository,$id,Request $request, Client $googleClient, CompanyRepository $companyRepository): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);

        $appointment = new Appointment();
        $appointment->setPatient($patient);
        $appointment->setCompany($user->getCompany());

        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);

        $patient->getAppointment()->add($appointment);

        $form = $this->createForm(PatientAppointmentType::class,$appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $appointment->setTitle($patient->getFirstName()." ". $patient->getLastName(). " " ."#".$patient->getId(). " ". $appointment->getTreatment(). " " .$patient->getPhone());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $sid = 'AC650d86e24b4d0dcd5e9b4cad1c26a770';
            $token = '97311b09fe3ea2b8c5acf028e99ca07a';

            $twilio = new \Twilio\Rest\Client($sid, $token);

            $patientPhone = $patient->getPhone();
            $areaCode = substr($patientPhone, 0, 3);

            if($areaCode == "+52"){
                $message = $twilio->messages
                    ->create($patient->getPhone(), // to
                        array(
                            "from" => "+12512548903",
                            "body" => "Hola ".$patient->getFirstName() . ". Tu cita esta agendada para la fecha: ".$appointment->getBeginAt()->format("d-M H:i")
                        )
                    );

            }else{
                $message = $twilio->messages
                    ->create("+52".$patient->getPhone(), // to
                        array(
                            "from" => "+12512548903",
                            "body" => "Hola ".$patient->getFirstName() . ". Tu cita esta agendada para la fecha: ".$appointment->getBeginAt()->format("d-M H:i")
                        )
                    );

            }





            date_default_timezone_set('America/Monterrey');

            if($company->getGoogleJson() != null) {
                $client = $googleClient->getClient($company->getGoogleJson()[0]);

                $service = new Google_Service_Calendar($client);

                $event = new Google_Service_Calendar_Event(array(
                    'summary' => $appointment->getTitle(),
                    'description' => $appointment->getNotes(),

                    'start' => array(
                        'dateTime' => $appointment->getBeginAt()->format(DateTime::RFC3339),
                        'timeZone' => 'America/Monterrey',


                    ),
                    'end' => array(
                        'dateTime' => $appointment->getEndAt()->format(DateTime::RFC3339),
                        'timeZone' => 'America/Monterrey',

                    )
                ));

                $calendarId = $appointment->getCalendar();

                switch ($appointment->getColor()) {
                    case 'red':
                        $event->setColorId('11');
                        break;
                    case 'orange':
                        $event->setColorId('6');
                        break;
                    case 'yellow':
                        $event->setColorId('5');
                        break;
                    case 'green':
                        $event->setColorId('10');
                        break;
                    case 'blue':
                        $event->setColorId('1');
                        break;
                    case 'purple':
                        $event->setColorId('3');
                        break;
                    case 'grey':
                        $event->setColorId('8');
                        break;
                }

                $event = $service->events->insert($calendarId, $event);
                $appointment->setEventId($event->getId());



                $entityManager->persist($appointment);
                $entityManager->flush();
            }

            return $this->redirectToRoute('patient_index');
        }

        return $this->render('patient/appointment.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/pdf", name="patient_pdf", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function pdf(PatientRepository $patientRepository, $id, Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = json_decode($patient->getCustomData()[0], true);

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $base = $this->renderView('base.html.twig');

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf_reports/mypdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            'patient' => $patient,
            'customdata' => $data
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream($patient->getId()."_report.pdf", [
            "Attachment" => false
        ]);


    }


    /**
     * @Route("/available", name="patients_fetch_available", methods={"GET"})
     * @param Request $request
     * @param PatientRepository $repository
     * @return JsonResponse
     */
    public function fetchAvailable(Request $request, PatientRepository $repository):JsonResponse
    {

        $user = $this->security->getUser();
        $result = $repository->findArrayByCompany($user->getCompany());


        return $this->json($result);


    }


    /**
     * @Route("/{id}/data/{formId}", name="patient_data_new", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function data(PatientRepository $patientRepository,$id,$formId,Request $request): Response
    {
        //TODO:Add dropdown to choose form to fill
        //TODO:Add export to pdf functionality

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);

        //$customData = $this->customDataRepository->findByCompany($user->getCompany());
        $customForm = $this->customFormRepository->findOneBy(['id' => $formId]);
        $dataForm = $this->createFormBuilder();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($customForm->getFields(), 'json', ['ignored_attributes' => ['company','customForm','id']]);;

        $customData =  $serializer->decode($jsonContent,'json');

        foreach ($customData as $cd){
            if($cd["type"] == "Int"){
                $dataForm->add($cd['name'], NumberType::class);
            }elseif($cd["type"] == "String"){
                $dataForm->add($cd['name'], TextType::class);
            }elseif($cd["type"] == "Boolean") {
                $dataForm->add($cd['name'], CheckboxType::class);
            }

        }

        $dataForm ->add('save', SubmitType::class, ['label' => 'Create Medical']);

        $form = $dataForm->getForm();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $form->handleRequest($request);
        $jsonContent = $serializer->serialize($form->getData(), 'json');

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                var_dump($jsonContent);
                $patient->setCustomData(array($jsonContent));
                $entityManager->persist($patient);
                $entityManager->flush();
               // $fs = new Filesystem();
               // $fs->dumpFile('json/'.$user->getCompany()->getName().'/'.$patient->getId() .'.json', $jsonContent);
            }
            catch(IOException $e) {
            }

            return $this->redirectToRoute('patient_index');
        }

        return $this->render('patient/data.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/{id}/data/edit/{formId}", name="patient_data_edit", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function data_edit(PatientRepository $patientRepository,$id,$formId,Request $request): Response
    {
        //TODO:Add dropdown to choose form to fill
        //TODO:Add export to pdf functionality

        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);
        $patientData = json_decode($patient->getCustomData()[0], true);
        //$customData = $this->customDataRepository->findByCompany($user->getCompany());
        $customForm = $this->customFormRepository->findOneBy(['id' => 11]);
        $dataForm = $this->createFormBuilder();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($customForm->getFields(), 'json', ['ignored_attributes' => ['company','customForm','id']]);;

        $customData =  $serializer->decode($jsonContent,'json');

        foreach ($customData as $cd){

            if($cd["type"] == "Int"){
                $dataForm->add($cd['name'], NumberType::class,['data'=> $patientData[$cd['name']]]);

            }elseif($cd["type"] == "String"){
                $dataForm->add($cd['name'], TextType::class,['data'=> $patientData[$cd['name']]]);

            }elseif($cd["type"] == "Boolean") {
                $dataForm->add($cd['name'], CheckboxType::class,['data'=> $patientData[$cd['name']]]);

            }

        }

        $dataForm ->add('save', SubmitType::class, ['label' => 'Create Medical']);

        $form = $dataForm->getForm();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $form->handleRequest($request);
        $jsonContent = $serializer->serialize($form->getData(), 'json');

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                var_dump($jsonContent);
                $patient->setCustomData(array($jsonContent));
                $entityManager->persist($patient);
                $entityManager->flush();
                // $fs = new Filesystem();
                // $fs->dumpFile('json/'.$user->getCompany()->getName().'/'.$patient->getId() .'.json', $jsonContent);
            }
            catch(IOException $e) {
            }

            return $this->redirectToRoute('patient_index');
        }

        return $this->render('patient/data.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/{id}", name="patient_show", methods={"GET"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(PatientRepository $patientRepository,CustomFormRepository $customFormRepository,AppointmentRepository $appointmentRepository, $id): Response
    {
        $user = $this->security->getUser();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);
        $customForms = $customFormRepository->findByCompany($user->getCompany());
        $appointments = $appointmentRepository->findOneByCompanyAndPatient($user->getCompany(),$id);
        return $this->render('patient/show.html.twig', [
            'patient' => $patient,
            'appointments'=> $appointments,
            'customForms'=>$customForms
        ]);
    }

    /**
     * @Route("/{id}/edit", name="patient_edit", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param $id
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(PatientRepository $patientRepository, $id,Request $request): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $id);
        $originalAddress = new ArrayCollection();
        $originalAppointment = new ArrayCollection();


        foreach ($patient->getAddress() as $address) {
            $originalAddress->add($address);
        }
         foreach ($patient->getAppointment() as $appointment) {
             $originalAppointment->add($appointment);
         }

        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // remove the relationship between the Treatment and the Equipment
            foreach ($originalAddress as $address) {
                if (false === $patient->getAddress()->contains($address)) {

                    $entityManager->remove($address);

                    $entityManager->persist($address);

                }
            }

            foreach ($originalAppointment as $appointment) {
                if (false === $patient->getAppointment()->contains($appointment)) {

                    $entityManager->remove($appointment);

                    $entityManager->persist($appointment);

                }
            }

            $entityManager->persist($patient);
            $entityManager->flush();

            return $this->redirectToRoute('patient_index');
        }


        return $this->render('patient/edit.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="patient_delete", methods={"DELETE"})
     * @param Request $request
     * @param Patient $patient
     * @return Response
     */
    public function delete(Request $request, Patient $patient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$patient->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($patient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('patient_index');
    }

    public function getPatientActions(Patient $patient){
        return $this->view($patient,Response::HTTP_OK);
    }

    public function getPatientsActions(){
        $data = $this->patientRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    public function getPatientAddressActions(Address $address){
        return $this->view($address,Response::HTTP_OK);
    }

    public function getPatientAppointmentActions(Appointment $appointment){
        return $this->view($appointment,Response::HTTP_OK);
    }

    public function getPatientMaritalstatusActions(MaritalStatus $maritalStatus){
        return $this->view($maritalStatus,Response::HTTP_OK);
    }


    /**
     * @Rest\RequestParam(name="firstName", description="First Name", nullable=false)
     * @Rest\RequestParam(name="lastName", description="Last Name", nullable=false)
     * @Rest\RequestParam(name="gender", description="Gender", nullable=false)
     * @Rest\RequestParam(name="maritalStatusId", description="Marital Status ID", nullable=false)
     * @Rest\RequestParam(name="birthday", description="Birthday", nullable=false)
     * @Rest\RequestParam(name="email", description="Email", nullable=true)
     * @Rest\RequestParam(name="phone", description="Phone", nullable=true)
     * @Rest\RequestParam(name="religion", description="Religion", nullable=true)
     * @Rest\RequestParam(name="companyId", description="Company", nullable=false)
     * @Rest\RequestParam(name="addressId", description="Address", nullable=true)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postPatientAction(ParamFetcher $paramFetcher){
        $firstName = $paramFetcher->get('firstName');
        $lastName = $paramFetcher->get('lastName');
        $gender = $paramFetcher->get('gender');


        if($gender == "Male"){
            $gender = "M";
        }else{
            $gender = "F";
        }

        $maritalStatusId = $paramFetcher->get('maritalStatusId');
        $maritalStatus = $this->maritalStatusRepository->findOneBy(['id' => $maritalStatusId]);

        $companyId = $paramFetcher->get('companyId');
        $company = $this->companyRepository->findOneBy(['id' => $companyId]);

        $addressId = $paramFetcher->get('addressId');
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        $birthday = $paramFetcher->get('birthday');

        $email = $paramFetcher->get('email');
        $phone = $paramFetcher->get('phone');
        $religion = $paramFetcher->get('religion');


        if($firstName){
            $patient = new Patient();
            $patient->setFirstName($firstName);
            $patient->setLastName($lastName);
            $patient->setGender($gender);
            $patient->setMaritalStatus($maritalStatus);
            $patient->setBirthdate(\DateTime::createFromFormat('Y-m-d', $birthday));
            $patient->setEmail($email);
            $patient->setPhone($phone);
            $patient->setReligion($religion);
            $patient->setCompany($company);

            if($addressId!= null)
            $patient->addAddres($address);

            $this->entityManager->persist($patient);
            $this->entityManager->flush();

            return $this->view($patient,Response::HTTP_CREATED);

        }
        return $this->view(['firstName' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\RequestParam(name="addressId", description="The new address", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @param Patient $patient
     * @return View
     */
    public function patchPatientAddressAction(ParamFetcher $paramFetcher, Patient $patient){
        $errors = [];

        $addressId = $paramFetcher->get('addressId');
        $address = $this->addressRepository->findOneBy(['id' => $addressId]);

        if($patient){
            $patient->addAddres($address);
            $this->entityManager->persist($patient);
            $this->entityManager->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT);
        }
        $errors[] = [
            'patient' => 'Patient not found'
        ];
        return $this->view($errors, Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\FileParam(name="image", description="Patients picture", nullable=false, image=true)
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     * @param Patient $patient
     * @return View
     */
    public function imagePatientAction(Request $request, ParamFetcher $paramFetcher, Patient $patient){

       $currentImage = $patient->getImage();
        if(!is_null($currentImage)){
            $filesystem = new Filesystem();
            $filesystem->remove(
                $this->getUploadsDir() . $currentImage
            );
        }

        $file = $paramFetcher->get('image');
        if($file){
            $filename = md5(uniqid()). '.' . "jpeg";
            $file->move(
                $this->getUploadsDir(),
                $filename);
            $patient->setImage($filename);
            $patient->setImagePath('/uploads/' . $filename);

            $this->entityManager->persist($patient);
            $this->entityManager->flush();

            $data = $request->getUriForPath($patient->getImagePath());
            return $this->view($data, Response::HTTP_OK);

        }
        return $this->view(['message' => 'Something went wrong'], Response::HTTP_BAD_REQUEST);
    }



    public  function  getUploadsDir(){
        return $this->getParameter('uploads_dir');

    }




}
