<?php

namespace App\Controller;

use Safe\DateTime;
use App\Entity\Event;
use App\Service\Client;
use App\Entity\Calendar;
use Google_Service_Calendar;
use App\Entity\EventTreatment;
use Google_Service_Calendar_Event;
use App\Repository\EventRepository;
use App\Repository\ClientRepository;
use App\Repository\CompanyRepository;
use App\Repository\CalendarRepository;
use App\Repository\TreatmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google\Service\Calendar\EventDateTime;
use Google_Service_Calendar_EventDateTime;
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
    public function index(CalendarRepository $calendarRepository,TreatmentRepository $treatmentRepository,Client $googleClient): Response
    {
        $client = $googleClient->getClient($this->security->getUser()->getCompany()->getGoogleJson()[0]);
        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();

        $googlecals = array();
        $service = new Google_Service_Calendar($client);
        $calendarList = $service->calendarList->listCalendarList();
        foreach ($calendarList->getItems() as $calendarListEntry) {
         //   $calendar = new Calendar();
          //  $calendar->setCompany($user->getCompany());
         //   $calendar->setName($calendarListEntry->getSummary());
          //  $calendar->setColor($calendarListEntry->getbackgroundColor());
          //  $calendar->setGoogleId($calendarListEntry->getId());
          //  $em->persist($calendar);
            array_push($googlecals,$calendarListEntry->getSummary());
          }
        
          //$em->flush();

        return $this->render('calendar/index.html.twig', [
           // 'lastChange' => $user->getCompany()->getLastCalendarChange()->format('d-m-Y')
            'googlecalendars' => $googlecals,
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
    public function CreateEvent(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository, Client $googleClient):JsonResponse
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
            $eventNotes = $request->request->get('notes');

        }
        else {
            die();
        }
        $client = $googleClient->getClient($this->security->getUser()->getCompany()->getGoogleJson()[0]);

        $service = new Google_Service_Calendar($client);

        $company = $user->getCompany();
        
        $company->setCalendarChange(new \DateTime());
        $em->persist($company);

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
        $event->setNote($eventNotes);

        
        $calendarId = $calendar->getGoogleId();
        
        
        $googleEvent = new Google_Service_Calendar_Event(array(
            'summary' => $client->getCode().' '. $client->getName() . ' ' . $client->getPhone(),
            'description' => $treatment->getName() . ' ' . $event->getNote(),
            
            'start' => array(
                'dateTime' => $dateStart->format(DateTime::RFC3339),
                'timeZone' => 'America/Monterrey',
                
                
            ),
            'end' => array(
                'dateTime' => $dateEnd->format(DateTime::RFC3339),
                'timeZone' => 'America/Monterrey',
                
                )
            ));
            
        $googleEventID = $service->events->insert($calendarId, $googleEvent);
            
        $event->setGoogleID($googleEventID->id);
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
    public function ResizeEvent(Request $request, EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository,Client $googleClient):JsonResponse
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

        $company = $user->getCompany();

        $company->setCalendarChange(new \DateTime());
        $em->persist($company);


        $dateEnd = new \DateTime($eventDay.' '.$eventTime);

        $event = $eventRepository->findByCompanyID($user->getCompany(),$eventID);
  
        $event->setEnd($dateEnd);

        $em->persist($event);
        $em->flush();

        $client = $googleClient->getClient($this->security->getUser()->getCompany()->getGoogleJson()[0]);

        $service = new Google_Service_Calendar($client);
        
        $googleEvent = $service->events->get($event->getCalendar()->getGoogleId(), $event->getGoogleID());
        
        $end = new EventDateTime();

        $end->setDateTime($dateEnd->format(DateTime::RFC3339));

        $googleEvent->setEnd($end);


        $updatedEvent = $service->events->update($event->getCalendar()->getGoogleId(), $event->getGoogleID(), $googleEvent);

        
        

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
    public function MoveEvent(Request $request,EventTreatmentRepository $eventTreatmentRepository, EventRepository $eventRepository,ClientRepository $clientRepository, TreatmentRepository $treatmentRepository, CalendarRepository $calendarRepository, Client $googleClient):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $eventID = $request->request->get('id');
            $eventDay = $request->request->get('day');
            $eventTime = $request->request->get('time');
            $endTime = $request->request->get('end');
            $endDay = $request->request->get('endDay');

        }
        else {
            die();
        }

        $company = $user->getCompany();

        $company->setCalendarChange(new \DateTime());
        $em->persist($company);

        $dateStart = new \DateTime($eventDay.' '.$eventTime);
        $dateEnd = new \DateTime($endDay.' '.$endTime);

        $event = $eventRepository->findByCompanyID($user->getCompany(),$eventID);
  
        $event->setStart($dateStart);
        $event->setEnd($dateEnd);

        $em->persist($event);
        $em->flush();

        $client = $googleClient->getClient($this->security->getUser()->getCompany()->getGoogleJson()[0]);

        $service = new Google_Service_Calendar($client);
        
        $googleEvent = $service->events->get($event->getCalendar()->getGoogleId(), $event->getGoogleID());
        
        $start = new EventDateTime();
        $end = new EventDateTime();

        $start->setDateTime($dateStart->format(DateTime::RFC3339)) ;
        $end->setDateTime($dateEnd->format(DateTime::RFC3339));

        $googleEvent->setStart($start);
        $googleEvent->setEnd($end);


        $updatedEvent = $service->events->update($event->getCalendar()->getGoogleId(), $event->getGoogleID(), $googleEvent);


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
     * @Route("/fetchlastchange", name="fetch_last_change", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function FetchLastChange(Request $request):JsonResponse
    {

        $user = $this->security->getUser();


        if ($request->getMethod() == 'POST')
        {
            $currentEventChange = $request->request->get('currentDate');
       
        }
        else {
            die();
        }

        $fixedDate = str_replace('"', "", $currentEventChange);
        $fixedDate = stripslashes($fixedDate);
   
        $lastDate = $user->getCompany()->getCalendarChange()->format('Y-m-d H:i:s');

        if($fixedDate == $lastDate){
            $response = json_encode(1);
        }else{
            $response = json_encode(0);
       }
        
        

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }

    /**
     * @Route("/lastchange", name="last_change", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function LastChange(Request $request,CompanyRepository $companyRepository):JsonResponse
    {

        $user = $this->security->getUser();

        if ($request->getMethod() == 'POST')
        {
       
        }
        else {
            die();
        }

        $calendarChange = $companyRepository->findByID($user->getCompany()->getId());

        $response = json_encode($user->getCompany()->getCalendarChange()->format('Y-m-d H:i:s'));
    
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

    /**
     * @Route("/eventfetch", name="event_fetch", methods={"POST"})
     * @param Request $request
     * @param EventRepository $eventRepository
     * @param CalendarRepository $calendarRepository
     * @return JsonResponse
     * @throws Exception
     */
    public function FetchEvent(Request $request, EventRepository $eventRepository, CalendarRepository $calendarRepository):JsonResponse
    {

        $user = $this->security->getUser();
        $em = $this->getDoctrine()->getManager();


        if ($request->getMethod() == 'POST')
        {
            $eventID = $request->request->get('id');
        
        }
        else {
            die();
        }

        $eventArray = $eventRepository->findByCompanyIDArray($user->getCompany(),$eventID);
        $event = $eventRepository->findByCompanyID($user->getCompany(),$eventID);

        $treatments = $event->getEventTreatments();

        $treatName = $treatments[0]->getTreatment()->getName();
        array_push($eventArray,$treatName);
        $response = json_encode($eventArray);

        $returnResponse = new JsonResponse();
        $returnResponse->setjson($response);

        return $returnResponse;

    }


}
