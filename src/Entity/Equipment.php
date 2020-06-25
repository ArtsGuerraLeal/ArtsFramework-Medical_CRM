<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EquipmentRepository")
 */
class Equipment
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $purchasedOn;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastUsedOn;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Treatment",mappedBy="equipment",cascade={"persist"})
     */
    private $treatment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="equipment")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    private $company;

    public function __construct()
    {
        $this->treatment = new ArrayCollection();
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

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPurchasedOn(): ?\DateTimeInterface
    {
        return $this->purchasedOn;
    }

    public function setPurchasedOn(?\DateTimeInterface $purchasedOn): self
    {
        $this->purchasedOn = $purchasedOn;

        return $this;
    }

    public function getLastUsedOn(): ?\DateTimeInterface
    {
        return $this->lastUsedOn;
    }

    public function setLastUsedOn(?\DateTimeInterface $lastUsedOn): self
    {
        $this->lastUsedOn = $lastUsedOn;

        return $this;
    }


    public function __toString() {
        return $this->name;
    }


    public function getTreatment(): Collection
    {
        return $this->treatment;
    }

    public function addTreatment(Treatment $treatment): self
    {
        if (!$this->treatment->contains($treatment)) {
            $this->treatment[] = $treatment;
            $treatment->setEquipment($this);
            $treatment->setCompany($this->getCompany());
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): self
    {
        if ($this->treatment->contains($treatment)) {
            $this->treatment->removeElement($treatment);
            // set the owning side to null (unless already changed)
            if ($treatment->getEquipment() === $this) {
                $treatment->setEquipment(null);
            }
        }

        return $this;
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
}
