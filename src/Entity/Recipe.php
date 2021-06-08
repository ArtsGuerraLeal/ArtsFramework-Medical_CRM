<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
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
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=RecipeMaterials::class, mappedBy="recipe")
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
            $recipeMaterial->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeMaterial(RecipeMaterials $recipeMaterial): self
    {
        if ($this->recipeMaterials->removeElement($recipeMaterial)) {
            // set the owning side to null (unless already changed)
            if ($recipeMaterial->getRecipe() === $this) {
                $recipeMaterial->setRecipe(null);
            }
        }

        return $this;
    }
}
