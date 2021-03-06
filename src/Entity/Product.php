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

    /**
     * @ORM\OneToMany(targetEntity=ProductStock::class, mappedBy="product")
     */
    private $productStocks;

    /**
     * @ORM\ManyToOne(targetEntity=Vendor::class, inversedBy="products")
     */
    private $vendor;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="product")
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity=ProductMaterial::class, mappedBy="product")
     */
    private $productMaterials;

    /**
     * @ORM\OneToMany(targetEntity=EnsambleProduct::class, mappedBy="product")
     */
    private $ensambleProducts;

    /**
     * @ORM\OneToOne(targetEntity=Ensamble::class, inversedBy="product", cascade={"persist", "remove"})
     */
    private $ensamble;

    /**
     * @ORM\OneToMany(targetEntity=ProjectProduct::class, mappedBy="product")
     */
    private $projectProducts;

    public function __construct()
    {
        $this->productSolds = new ArrayCollection();
        $this->productQuotes = new ArrayCollection();
        $this->productOrdereds = new ArrayCollection();
        $this->productStocks = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->productMaterials = new ArrayCollection();
        $this->ensambleProducts = new ArrayCollection();
        $this->projectProducts = new ArrayCollection();
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

    /**
     * @return Collection|ProductStock[]
     */
    public function getProductStocks(): Collection
    {
        return $this->productStocks;
    }

    public function addProductStock(ProductStock $productStock): self
    {
        if (!$this->productStocks->contains($productStock)) {
            $this->productStocks[] = $productStock;
            $productStock->setProduct($this);
        }

        return $this;
    }

    public function removeProductStock(ProductStock $productStock): self
    {
        if ($this->productStocks->removeElement($productStock)) {
            // set the owning side to null (unless already changed)
            if ($productStock->getProduct() === $this) {
                $productStock->setProduct(null);
            }
        }

        return $this;
    }

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(?Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    /**
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setProduct($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getProduct() === $this) {
                $recipe->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductMaterial[]
     */
    public function getProductMaterials(): Collection
    {
        return $this->productMaterials;
    }

    public function addProductMaterial(ProductMaterial $productMaterial): self
    {
        if (!$this->productMaterials->contains($productMaterial)) {
            $this->productMaterials[] = $productMaterial;
            $productMaterial->setProduct($this);
        }

        return $this;
    }

    public function removeProductMaterial(ProductMaterial $productMaterial): self
    {
        if ($this->productMaterials->removeElement($productMaterial)) {
            // set the owning side to null (unless already changed)
            if ($productMaterial->getProduct() === $this) {
                $productMaterial->setProduct(null);
            }
        }

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
            $ensambleProduct->setProduct($this);
        }

        return $this;
    }

    public function removeEnsambleProduct(EnsambleProduct $ensambleProduct): self
    {
        if ($this->ensambleProducts->removeElement($ensambleProduct)) {
            // set the owning side to null (unless already changed)
            if ($ensambleProduct->getProduct() === $this) {
                $ensambleProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getEnsamble(): ?Ensamble
    {
        return $this->ensamble;
    }

    public function setEnsamble(?Ensamble $ensamble): self
    {
        $this->ensamble = $ensamble;

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
            $projectProduct->setProduct($this);
        }

        return $this;
    }

    public function removeProjectProduct(ProjectProduct $projectProduct): self
    {
        if ($this->projectProducts->removeElement($projectProduct)) {
            // set the owning side to null (unless already changed)
            if ($projectProduct->getProduct() === $this) {
                $projectProduct->setProduct(null);
            }
        }

        return $this;
    }
}
