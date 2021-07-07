<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AreaRepository::class)
 */
class Area
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="areas")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=ProjectArea::class, mappedBy="area")
     */
    private $projectAreas;

    /**
     * @ORM\OneToMany(targetEntity=ProjectProduct::class, mappedBy="area")
     */
    private $projectProducts;

    public function __construct()
    {
        $this->projectAreas = new ArrayCollection();
        $this->projectProducts = new ArrayCollection();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|ProjectArea[]
     */
    public function getProjectAreas(): Collection
    {
        return $this->projectAreas;
    }

    public function addProjectArea(ProjectArea $projectArea): self
    {
        if (!$this->projectAreas->contains($projectArea)) {
            $this->projectAreas[] = $projectArea;
            $projectArea->setArea($this);
        }

        return $this;
    }

    public function removeProjectArea(ProjectArea $projectArea): self
    {
        if ($this->projectAreas->removeElement($projectArea)) {
            // set the owning side to null (unless already changed)
            if ($projectArea->getArea() === $this) {
                $projectArea->setArea(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectProduct[]
     */
    public function getProjectProducts(): Collection
    {
        return $this->projectProducts;
    }

    public function addProjectProduct(ProjectProduct $projectProduct): self
    {
        if (!$this->projectProducts->contains($projectProduct)) {
            $this->projectProducts[] = $projectProduct;
            $projectProduct->setArea($this);
        }

        return $this;
    }

    public function removeProjectProduct(ProjectProduct $projectProduct): self
    {
        if ($this->projectProducts->removeElement($projectProduct)) {
            // set the owning side to null (unless already changed)
            if ($projectProduct->getArea() === $this) {
                $projectProduct->setArea(null);
            }
        }

        return $this;
    }
}
