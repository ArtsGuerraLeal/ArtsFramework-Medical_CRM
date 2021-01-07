<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSold", mappedBy="product")
     */
    private $productSolds;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTaxable;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isMultiple;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="products")
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductQuote", mappedBy="product")
     */
    private $productQuotes;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrdered::class, mappedBy="product")
     */
    private $productOrdereds;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sku;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $upc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cost;

    public function __construct()
    {
        $this->productSolds = new ArrayCollection();
        $this->productQuotes = new ArrayCollection();
        $this->productOrdereds = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|ProductSold[]
     */
    public function getProductSolds(): Collection
    {
        return $this->productSolds;
    }

    public function addProductSold(ProductSold $productSold): self
    {
        if (!$this->productSolds->contains($productSold)) {
            $this->productSolds[] = $productSold;
            $productSold->setProduct($this);
        }

        return $this;
    }

    public function removeProductSold(ProductSold $productSold): self
    {
        if ($this->productSolds->contains($productSold)) {
            $this->productSolds->removeElement($productSold);
            // set the owning side to null (unless already changed)
            if ($productSold->getProduct() === $this) {
                $productSold->setProduct(null);
            }
        }

        return $this;
    }

    public function getIsTaxable(): ?bool
    {
        return $this->isTaxable;
    }

    public function setIsTaxable(?bool $isTaxable): self
    {
        $this->isTaxable = $isTaxable;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsMultiple(): ?bool
    {
        return $this->isMultiple;
    }

    public function setIsMultiple(?bool $isMultiple): self
    {
        $this->isMultiple = $isMultiple;

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
     * @return Collection|ProductQuote[]
     */
    public function getProductQuotes(): Collection
    {
        return $this->productQuotes;
    }

    public function addProductQuote(ProductQuote $productQuote): self
    {
        if (!$this->productQuotes->contains($productQuote)) {
            $this->productQuotes[] = $productQuote;
            $productQuote->setProduct($this);
        }

        return $this;
    }

    public function removeProductQuote(ProductQuote $productQuote): self
    {
        if ($this->productQuotes->contains($productQuote)) {
            $this->productQuotes->removeElement($productQuote);
            // set the owning side to null (unless already changed)
            if ($productQuote->getProduct() === $this) {
                $productQuote->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductOrdered[]
     */
    public function getProductOrdereds(): Collection
    {
        return $this->productOrdereds;
    }

    public function addProductOrdered(ProductOrdered $productOrdered): self
    {
        if (!$this->productOrdereds->contains($productOrdered)) {
            $this->productOrdereds[] = $productOrdered;
            $productOrdered->setProduct($this);
        }

        return $this;
    }

    public function removeProductOrdered(ProductOrdered $productOrdered): self
    {
        if ($this->productOrdereds->removeElement($productOrdered)) {
            // set the owning side to null (unless already changed)
            if ($productOrdered->getProduct() === $this) {
                $productOrdered->setProduct(null);
            }
        }

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getUpc(): ?string
    {
        return $this->upc;
    }

    public function setUpc(?string $upc): self
    {
        $this->upc = $upc;

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
}
