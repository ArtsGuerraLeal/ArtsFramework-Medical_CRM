<?php

namespace App\Serializer;

use App\Entity\Address;
use App\Entity\Appointment;
use App\Entity\CustomData;
use App\Entity\Equipment;
use App\Entity\Patient;
use App\Entity\State;
use App\Entity\Treatment;
use Symfony\Component\Routing\RouterInterface;


class CircularReferenceHandler
{

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    function __invoke($object)
    {
        switch ($object){
            case $object instanceof Address:
                return $this->router->generate('get_address',['address' => $object->getId()]);
            case $object instanceof State:
                return $this->router->generate('get_state',['state' => $object->getId()]);
            case $object instanceof Equipment:
                return $this->router->generate('get_equipment',['equipment' => $object->getId()]);
            case $object instanceof Treatment:
                return $this->router->generate('get_treatment',['treatment' => $object->getId()]);
            case $object instanceof Appointment:
                return $this->router->generate('get_appointment',['appointment' => $object->getId()]);
            case $object instanceof Patient:
                return $this->router->generate('get_patient',['patient' => $object->getId()]);
            case $object instanceof CustomData:
                return $this->router->generate('get_CustomData',['CustomData' => $object->getId()]);
        }
        $object->getId();
    }

}
