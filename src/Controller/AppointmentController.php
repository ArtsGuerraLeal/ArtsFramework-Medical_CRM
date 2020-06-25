<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Patient;
use App\Form\AppointmentType;
use App\Form\FollowupType;
use App\Form\PatientAppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\CompanyRepository;
use App\Repository\PatientRepository;
use App\Repository\TreatmentRepository;
use App\Service\Client;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Security;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

/**
 * @Route("/appointment")
 */
class AppointmentController extends AbstractFOSRestController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var AppointmentRepository
     */
    private $appointmentRepository;
    /**
     * @var PatientRepository
     */
    private $patientRepository;
    /**
     * @var TreatmentRepository
     */
    private $treatmentRepository;

    private $security;

    public function __construct(PatientRepository $patientRepository, AppointmentRepository $appointmentRepository, TreatmentRepository $treatmentRepository, EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->appointmentRepository = $appointmentRepository;
        $this->patientRepository = $patientRepository;
        $this->treatmentRepository = $treatmentRepository;
        $this->security = $security;

    }
    
    /**
     * @Route("/", name="appointment_index", methods={"GET"})
     * @param AppointmentRepository $appointmentRepository
     * @return Response
     */
    public function index(AppointmentRepository $appointmentRepository): Response
    {
        $user = $this->security->getUser();
        return $this->render('appointment/index.html.twig', [
            'appointments' => $appointmentRepository->findByCompany($user->getCompany()),
        ]);
    }

    /**
     * @Route("/calendar", name="appointment_calendar", methods={"GET"})
     */
    public function calendar(): Response
    {
        return $this->render('appointment/calendar.html.twig');
    }

    /**
     * @Route("/new", name="appointment_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $appointment = new Appointment();
        $user = $this->security->getUser();
        $appointment->setCompany($user->getCompany());
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($appointment);
            $entityManager->flush();

            return $this->redirectToRoute('appointment_index');
        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/followup", name="appointment_followup", methods={"GET","POST"})
     * @param PatientRepository $patientRepository
     * @param Request $request
     * @param $id
     * @param Client $googleClient
     * @param CompanyRepository $companyRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function followup(AppointmentRepository $appointmentRepository, PatientRepository $patientRepository,Request $request,$id, Client $googleClient,CompanyRepository $companyRepository): Response
    {
        $user = $this->security->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $prevAppointment = $appointmentRepository->findOneByCompanyID($user->getCompany(),$id);

        $patient = $patientRepository->findOneByCompanyID($user->getCompany(), $prevAppointment->getPatient());

        $appointment = new Appointment();
        $appointment->setPatient($patient);
        $appointment->setCompany($user->getCompany());
        $appointment->setAppointment($prevAppointment);
        $appointment->setTreatment($prevAppointment->getTreatment());

        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);

        $patient->getAppointment()->add($appointment);

        $form = $this->createForm(FollowupType::class,$appointment);
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

        return $this->render('appointment/followup.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="appointment_show", methods={"GET"})
     * @param AppointmentRepository $appointmentRepository
     * @param $id
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(AppointmentRepository $appointmentRepository,$id): Response
    {
        $user = $this->security->getUser();
        $appointment = $appointmentRepository->findOneByCompanyID($user->getCompany(), $id);
        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="appointment_edit", methods={"GET","POST"})
     * @param Request $request
     * @param AppointmentRepository $appointmentRepository
     * @param $id
     * @param Client $googleClient
     * @param CompanyRepository $companyRepository
     * @return Response
     * @throws NonUniqueResultException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function edit(Request $request, AppointmentRepository $appointmentRepository, $id, Client $googleClient, CompanyRepository $companyRepository): Response
    {
        $user = $this->security->getUser();
        $appointment = $appointmentRepository->findOneByCompanyID($user->getCompany(), $id);
        $company = $companyRepository->findOneBy(['id'=>$user->getCompany()->getId()]);
        $entityManager = $this->getDoctrine()->getManager();

        $originalCalendar = $appointment->getCalendar();

        $form = $this->createForm(PatientAppointmentType::class, $appointment);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {



            $appointment->setTitle($appointment->getPatient()->getFirstName()." ". $appointment->getPatient()->getLastName(). " " ."#".$appointment->getPatient()->getId(). " ". $appointment->getTreatment(). " " .$appointment->getPatient()->getPhone());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $sid = 'AC650d86e24b4d0dcd5e9b4cad1c26a770';
            $token = '97311b09fe3ea2b8c5acf028e99ca07a';

            $twilio = new \Twilio\Rest\Client($sid, $token);

            $message = $twilio->messages
                ->create("+52".$appointment->getPatient()->getPhone(), // to
                    array(
                        "from" => "+12512548903",
                        "body" => "Hola ".$appointment->getPatient()->getFirstName() . ". Tu cita se cambio para la fecha: ".$appointment->getBeginAt()->format("d-M H:i")
                    )
                );


            date_default_timezone_set('America/Monterrey');

            if($company->getGoogleJson() != null) {
                $client = $googleClient->getClient($company->getGoogleJson()[0]);

                $service = new Google_Service_Calendar($client);

                $event = $service->events->get($originalCalendar, $appointment->getEventId());

                $start = new Google_Service_Calendar_EventDateTime();
                $end = new Google_Service_Calendar_EventDateTime();

                $start->setDateTime($appointment->getBeginAt()->format(DateTime::RFC3339));
                $end->setDateTime($appointment->getEndAt()->format(DateTime::RFC3339));



                $event->setStart($start);

                $event->setEnd($end);

                $event->setSummary($appointment->getTitle());
                $event->setDescription($appointment->getNotes());



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

                $updatedEvent = $service->events->update($originalCalendar,$appointment->getEventId(), $event);
                $appointment->setEventId($updatedEvent->getId());

                if($originalCalendar!=$appointment->getCalendar()){
                    $movedEvent = $service->events->move($originalCalendar,$appointment->getEventId(),$form['calendar']->getData());
                    $appointment->setEventId($movedEvent->getId());


                }





                $entityManager->persist($appointment);
                $entityManager->flush();
            }

            return $this->redirectToRoute('appointment_index');
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="appointment_delete", methods={"DELETE"})
     * @param Request $request
     * @param Appointment $appointment
     * @return Response
     */
    public function delete(Request $request, Appointment $appointment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($appointment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('appointment_index');
    }

    public function getAppointmentActions(Appointment $appointment){
        return $this->view($appointment,Response::HTTP_OK);
    }

    public function getAppointmentsActions(){
        $data = $this->appointmentRepository->findAll();
        return $this->view($data, Response::HTTP_OK);
    }

    public function getAppointmentPatientActions(Patient $patient){
        return $this->view($patient,Response::HTTP_OK);
    }

    /**
     * @Rest\RequestParam(name="title", description="Title", nullable=false)
     * @Rest\RequestParam(name="beginAt", description="beginAt", nullable=false)
     * @Rest\RequestParam(name="endAt", description="endAt", nullable=false)
     * @Rest\RequestParam(name="treatmentId", description="treatmentId", nullable=false)
     * @Rest\RequestParam(name="patientId", description="patientId", nullable=false)
     * @Rest\RequestParam(name="color", description="color", nullable=true)
     * @param ParamFetcher $paramFetcher
     * @return View
     */
    public function postAppointmentAction(ParamFetcher $paramFetcher){
        $title = $paramFetcher->get('title');
        $beginAt = $paramFetcher->get('beginAt');
        $endAt = $paramFetcher->get('endAt');

        $treatmentId = $paramFetcher->get('treatmentId');
        $treatment = $this->treatmentRepository->findOneBy(['id' => $treatmentId]);

        $patientId = $paramFetcher->get('patientId');
        $patient = $this->patientRepository->findOneBy(['id' => $patientId]);

        $color = $paramFetcher->get('color');

        if($patientId){
            $appointment = new Appointment();
            $appointment->setTitle($title);
            $appointment->setBeginAt($beginAt);
            $appointment->setEndAt($endAt);
            $appointment->setTreatment($treatment);
            $appointment->setPatient($patient);
            $appointment->setColor($color);

            $this->entityManager->persist($appointment);
            $this->entityManager->flush();
            return $this->view($appointment,Response::HTTP_CREATED);

        }
        return $this->view(['title' => 'this cannot be null'],Response::HTTP_BAD_REQUEST);
    }
}
