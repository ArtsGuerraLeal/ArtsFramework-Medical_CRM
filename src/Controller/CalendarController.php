<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventTreatment;
use App\Repository\EventRepository;
use App\Repository\ClientRepository;
use App\Repository\CalendarRepository;
use App\Repository\TreatmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\EventTreatmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{

    private $security;
    
   /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager,Security $security){
        $this->entityManager = $entityManager;
        $this->security = $security;

    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function index(CalendarRepository $calendarRepository,TreatmentRepository $treatmentRepository): Response
    {
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
            'treatments'=> $treatmentRepository->findByCompany($this->security->getUser()->getCompany())
        ]);
    }

    /**
     * @Route("/eventcreate", name="event_create", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function CreateEvent(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $clientName = $request->request->get('client');
            $treatmentID = $request->request->get('treatment');
            $eventDay = $request->request->get('day');
            $eventTime = $request->request->get('time');
            $eventCalendar = $request->request->get('calendar');

        }
        else {
            die();
        }
        $dateStart = new \DateTime($eventDay.' '.$eventTime);
        $dateEnd = new \DateTime($eventDay.' '.$eventTime);

        $eventTreatments = $eventTreatmentRepository->FindSameSlot($user->getCompany(),$dateStart,$dateEnd);
        
        
        
        $event = new Event();
        $client = $clientRepository->searchOneByName($clientName,$user->getCompany());
        $treatment = $treatmentRepository->findOneByCompanyID($user->getCompany(),$treatmentID);
        $response = 2;

        foreach ($eventTreatments as $eventTreatment) {
            if($eventTreatment->getTreatment()->getEquipment() == $treatment->getEquipment()){
                $response = 1;
            }else{
                $response = 0;
            }

        }

        $eventTreatment = new EventTreatment();
        $eventTreatment->setTreatment($treatment);
        $eventTreatment->setEvent($event);
        $eventTreatment->setCompany($user->getCompany());
        $em->persist($eventTreatment);
        $calendar = $calendarRepository->searchOneByName($eventCalendar,$user->getCompany());;
        
        $event->setCompany($user->getCompany());
        $event->setClient($client);
        $event->setCalendar($calendar);
        $event->setTitle($clientName. " " . $treatment->getName());
        $event->setStart($dateStart);
        $event->setEnd($dateEnd->modify("+30 minutes"));

        $em->persist($event);
        $em->flush();
        
        

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/eventresize", name="event_resize", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function ResizeEvent(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $eventID = $request->request->get('id');
            $eventDay = $request->request->get('day');
            $eventTime = $request->request->get('time');
        }
        else {
            die();
        }
        $dateEnd = new \DateTime($eventDay.' '.$eventTime);

        $event = $eventRepository->findByCompanyID($user->getCompany(),$eventID);
  
        $event->setEnd($dateEnd);

        $em->persist($event);
        $em->flush();
        
        

        $returnResponse = new JsonResponse();
        $returnResponse->setjson(1);

        return $returnResponse;

    }

    /**
     * @Route("/eventmove", name="event_move", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function MoveEvent(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $eventID = $request->request->get('id');
            $eventDay = $request->request->get('day');
            $eventTime = $request->request->get('time');
        }
        else {
            die();
        }
        $dateStart = new \DateTime($eventDay.' '.$eventTime);
        $dateEnd = new \DateTime($eventDay.' '.$eventTime);

        $event = $eventRepository->findByCompanyID($user->getCompany(),$eventID);
  
        $event->setStart($dateStart);
        $event->setEnd($dateEnd->modify("+30 minutes"));

        $em->persist($event);
        $em->flush();
        
        

        $returnResponse = new JsonResponse();
        $returnResponse->setjson(1);

        return $returnResponse;

    }

    /**
     * @Route("/eventslotcheck", name="event_slot_check", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function CheckEventSlot(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $clientName = $request->request->get('client');
            $treatmentID = $request->request->get('treatment');
            $eventDay = $request->request->get('day');
            $eventTime = $request->request->get('time');

        }
        else {
            die();
        }
        $dateStart = new \DateTime($eventDay.' '.$eventTime);
        $dateEnd = new \DateTime($eventDay.' '.$eventTime);

        $eventTreatments = $eventTreatmentRepository->FindSameSlot($user->getCompany(),$dateStart,$dateEnd);
        
        $treatment = $treatmentRepository->findOneByCompanyID($user->getCompany(),$treatmentID);
        $response = 2;

        foreach ($eventTreatments as $eventTreatment) {
            if($eventTreatment->getTreatment()->getEquipment() == $treatment->getEquipment()){
                $response = 1;
            }else{
                $response = 0;
            }

        }  

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/eventsfetch", name="events_fetch", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function FetchEvents(Request $request, EventRepository $eventRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $startDate = $request->request->get('start');
            $endDate = $request->request->get('end');
        
        }
        else {
            die();
        }

        $dateStart = new \DateTime($startDate);
        $dateEnd = new \DateTime($endDate);


        $events = $eventRepository->findAllByCompanyDate($user->getCompany(),$dateStart,$dateEnd);
       
        
        $response = json_encode($events);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }


}
