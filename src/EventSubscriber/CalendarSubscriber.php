<?php

namespace App\EventSubscriber;

use App\Repository\AppointmentRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $appointmentRepository;
    private $router;
    private $security;

    public function __construct(
        AppointmentRepository $appointmentRepository,
        UrlGeneratorInterface $router,
        Security $security
    ) {
        $this->appointmentRepository = $appointmentRepository;
        $this->router = $router;
        $this->security = $security;

    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        $user = $this->security->getUser();

        // Modify the query to fit to your entity and needs
        // Change appointment.beginAt by your start date property
        $appointments = $this->appointmentRepository
            ->createQueryBuilder('appointment')
            ->where('appointment.beginAt BETWEEN :start and :end OR appointment.endAt BETWEEN :start and :end')
            ->andWhere('appointment.company = :company')
            ->setParameter('company', $user->getCompany())
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;

        foreach ($appointments as $appointment) {
            // this create the events with your data (here appointment data) to fill calendar
            $appointmentEvent = new Event(
                $appointment->getTitle(),
                $appointment->getBeginAt(),
                $appointment->getEndAt() // If the end date is null or not defined, a all day event is created.

            );

            if($appointment->getColor() == 'yellow'){
                $appointmentEvent->setOptions([
                    'backgroundColor' => $appointment->getColor(),
                    'borderColor' => $appointment->getColor(),
                    'textColor' => 'black'
                ]);
                }else{
                $appointmentEvent->setOptions([
                    'backgroundColor' => $appointment->getColor(),
                    'borderColor' => $appointment->getColor(),
                    'textColor' => 'white'
                ]);
            }




            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */


            $appointmentEvent->addOption(
                'url',
                $this->router->generate('appointment_show', [
                    'id' => $appointment->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($appointmentEvent);
        }
    }
}