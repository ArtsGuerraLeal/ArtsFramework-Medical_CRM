<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StaffRepository")
 */
class Staff
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="staff")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StaffPositions", inversedBy="staff")
     */
    private $position;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appointment", mappedBy="staff")
     */
    private $appointments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SimpleAppointment", mappedBy="staff")
     */
    private $simpleAppointments;



    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->simpleAppointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
    

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPosition(): ?StaffPositions
    {
        return $this->position;
    }

    public function setPosition(?StaffPositions $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function __toString() {
        return $this->firstname." ".$this->lastname;
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
            $appointment->setStaff($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->contains($appointment)) {
            $this->appointments->removeElement($appointment);
            // set the owning side to null (unless already changed)
            if ($appointment->getStaff() === $this) {
                $appointment->setStaff(null);
            }
        }

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
            $simpleAppointment->setStaff($this);
        }

        return $this;
    }

    public function removeSimpleAppointment(SimpleAppointment $simpleAppointment): self
    {
        if ($this->simpleAppointments->contains($simpleAppointment)) {
            $this->simpleAppointments->removeElement($simpleAppointment);
            // set the owning side to null (unless already changed)
            if ($simpleAppointment->getStaff() === $this) {
                $simpleAppointment->setStaff(null);
            }
        }

        return $this;
    }


}
