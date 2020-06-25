<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CustomFormRepository")
 */
class CustomForm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $fields = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomData", mappedBy="customForm", cascade={"persist"})
     */
    private $CustomData;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="customForms")
     */
    private $company;

    public function __construct()
    {
        $this->CustomData = new ArrayCollection();
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

    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(?array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return Collection|CustomData[]
     */
    public function getCustomData(): Collection
    {
        return $this->CustomData;
    }

    public function addCustomDaton(CustomData $customData): self
    {
        if (!$this->CustomData->contains($customData)) {
            $this->CustomData[] = $customData;
            $customData->setCustomForm($this);
            $customData->setCompany($this->getCompany());
        }

        return $this;
    }

    public function removeCustomDaton(CustomData $customData): self
    {
        if ($this->CustomData->contains($customData)) {
            $this->CustomData->removeElement($customData);
            // set the owning side to null (unless already changed)
            if ($customData->getCustomForm() === $this) {
                $customData->setCustomForm(null);
            }
        }

        return $this;
    }

    public function addCustomData(CustomData $customData): self
    {
        if (!$this->CustomData->contains($customData)) {
            $this->CustomData[] = $customData;
            $customData->setCustomForm($this);
            $customData->setCompany($this->getCompany());
        }

        return $this;
    }

    public function removeCustomData(CustomData $customData): self
    {
        if ($this->CustomData->contains($customData)) {
            $this->CustomData->removeElement($customData);
            // set the owning side to null (unless already changed)
            if ($customData->getCustomForm() === $this) {
                $customData->setCustomForm(null);
            }
        }

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
}
