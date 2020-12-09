<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SimpleAppointmentRepository")
 */
class SimpleAppointment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Staff", inversedBy="simpleAppointments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $staff;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Treatment", inversedBy="simpleAppointments")
     */
    private $treatment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(?Staff $staff): self
    {
        $this->staff = $staff;

        return $this;
    }

    public function getTreatment(): ?Treatment
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatment $treatment): self
    {
        $this->treatment = $treatment;

        return $this;
    }
}
