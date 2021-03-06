<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TreatmentRepository")
 */
class Treatment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $cost;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeToComplete;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipment", inversedBy="treatment")
     * @ORM\JoinColumn(name="equipment_id", referencedColumnName="id")
     */
    private $equipment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="treatment")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Appointment", mappedBy="treatments")
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SimpleAppointment", mappedBy="treatment")
     */
    private $simpleAppointments;

    /**
     * @ORM\OneToMany(targetEntity=EventTreatment::class, mappedBy="treatment")
     */
    private $eventTreatments;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->simpleAppointments = new ArrayCollection();
        $this->eventTreatments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getEquipment()
    {
        return $this->equipment;
    }

    public function setEquipment(Equipment $equipment = null)
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company = null): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->addTreatment($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->contains($appointment)) {
            $this->appointments->removeElement($appointment);
            $appointment->removeTreatment($this);
        }

        return $this;
    }

    public function getTimeToComplete(): ?int
    {
        return $this->timeToComplete;
    }

    public function setTimeToComplete(int $timeToComplete): self
    {
        $this->timeToComplete = $timeToComplete;

        return $this;
    }

    /**
     * @return Collection|SimpleAppointment[]
     */
    public function getSimpleAppointments(): Collection
    {
        return $this->simpleAppointments;
    }

    public function addSimpleAppointment(SimpleAppointment $simpleAppointment): self
    {
        if (!$this->simpleAppointments->contains($simpleAppointment)) {
            $this->simpleAppointments[] = $simpleAppointment;
            $simpleAppointment->setTreatment($this);
        }

        return $this;
    }

    public function removeSimpleAppointment(SimpleAppointment $simpleAppointment): self
    {
        if ($this->simpleAppointments->contains($simpleAppointment)) {
            $this->simpleAppointments->removeElement($simpleAppointment);
            // set the owning side to null (unless already changed)
            if ($simpleAppointment->getTreatment() === $this) {
                $simpleAppointment->setTreatment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EventTreatment[]
     */
    public function getEventTreatments(): Collection
    {
        return $this->eventTreatments;
    }

    public function addEventTreatment(EventTreatment $eventTreatment): self
    {
        if (!$this->eventTreatments->contains($eventTreatment)) {
            $this->eventTreatments[] = $eventTreatment;
            $eventTreatment->setTreatment($this);
        }

        return $this;
    }

    public function removeEventTreatment(EventTreatment $eventTreatment): self
    {
        if ($this->eventTreatments->removeElement($eventTreatment)) {
            // set the owning side to null (unless already changed)
            if ($eventTreatment->getTreatment() === $this) {
                $eventTreatment->setTreatment(null);
            }
        }

        return $this;
    }
}
