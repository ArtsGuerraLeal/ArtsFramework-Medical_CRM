<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MaterialRepository::class)
 */
class Material
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
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="materials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity=RecipeMaterials::class, mappedBy="material")
     */
    private $recipeMaterials;

    public function __construct()
    {
        $this->recipeMaterials = new ArrayCollection();
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

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

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

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection|RecipeMaterials[]
     */
    public function getRecipeMaterials(): Collection
    {
        return $this->recipeMaterials;
    }

    public function addRecipeMaterial(RecipeMaterials $recipeMaterial): self
    {
        if (!$this->recipeMaterials->contains($recipeMaterial)) {
            $this->recipeMaterials[] = $recipeMaterial;
            $recipeMaterial->setMaterial($this);
        }

        return $this;
    }

    public function removeRecipeMaterial(RecipeMaterials $recipeMaterial): self
    {
        if ($this->recipeMaterials->removeElement($recipeMaterial)) {
            // set the owning side to null (unless already changed)
            if ($recipeMaterial->getMaterial() === $this) {
                $recipeMaterial->setMaterial(null);
            }
        }

        return $this;
    }
}
