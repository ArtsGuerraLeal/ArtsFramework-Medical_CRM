<?php

namespace App\Entity;

use App\Repository\EnsambleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnsambleRepository::class)
 */
class Ensamble
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cost;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="ensambles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=EnsambleProduct::class, mappedBy="ensamble")
     */
    private $ensambleProducts;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, mappedBy="ensamble", cascade={"persist", "remove"})
     */
    private $product;

    public function __construct()
    {
        $this->ensambleProducts = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

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
     * @return Collection|EnsambleProduct[]
     */
    public function getEnsambleProducts(): Collection
    {
        return $this->ensambleProducts;
    }

    public function addEnsambleProduct(EnsambleProduct $ensambleProduct): self
    {
        if (!$this->ensambleProducts->contains($ensambleProduct)) {
            $this->ensambleProducts[] = $ensambleProduct;
            $ensambleProduct->setEnsamble($this);
        }

        return $this;
    }

    public function removeEnsambleProduct(EnsambleProduct $ensambleProduct): self
    {
        if ($this->ensambleProducts->removeElement($ensambleProduct)) {
            // set the owning side to null (unless already changed)
            if ($ensambleProduct->getEnsamble() === $this) {
                $ensambleProduct->setEnsamble(null);
            }
        }

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        // unset the owning side of the relation if necessary
        if ($product === null && $this->product !== null) {
            $this->product->setEnsamble(null);
        }

        // set the owning side of the relation if necessary
        if ($product !== null && $product->getEnsamble() !== $this) {
            $product->setEnsamble($this);
        }

        $this->product = $product;

        return $this;
    }
}
