<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="events")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Calendar::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $calendar;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity=EventTreatment::class, mappedBy="event")
     */
    private $eventTreatments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    public function __construct()
    {
        $this->eventTreatments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    public function setCalendar(?Calendar $calendar): self
    {
        $this->calendar = $calendar;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

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
            $eventTreatment->setEvent($this);
        }

        return $this;
    }

    public function removeEventTreatment(EventTreatment $eventTreatment): self
    {
        if ($this->eventTreatments->removeElement($eventTreatment)) {
            // set the owning side to null (unless already changed)
            if ($eventTreatment->getEvent() === $this) {
                $eventTreatment->setEvent(null);
            }
        }

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
