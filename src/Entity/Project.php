<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="projects")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="projects")
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=ProjectArea::class, mappedBy="project")
     */
    private $projectAreas;

    /**
     * @ORM\OneToMany(targetEntity=ProjectProduct::class, mappedBy="project")
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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
            $projectArea->setProject($this);
        }

        return $this;
    }

    public function removeProjectArea(ProjectArea $projectArea): self
    {
        if ($this->projectAreas->removeElement($projectArea)) {
            // set the owning side to null (unless already changed)
            if ($projectArea->getProject() === $this) {
                $projectArea->setProject(null);
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
            $projectProduct->setProject($this);
        }

        return $this;
    }

    public function removeProjectProduct(ProjectProduct $projectProduct): self
    {
        if ($this->projectProducts->removeElement($projectProduct)) {
            // set the owning side to null (unless already changed)
            if ($projectProduct->getProject() === $this) {
                $projectProduct->setProject(null);
            }
        }

        return $this;
    }
}
