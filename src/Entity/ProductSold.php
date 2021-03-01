<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductSoldRepository")
 */
class ProductSold
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productSolds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sale;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discount", mappedBy="productSold")
     */
    private $discounts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="productSolds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discountReason;

    /**
     * @ORM\ManyToOne(targetEntity=Discount::class, inversedBy="productSolds")
     */
    private $discountId;

    /**
     * @ORM\OneToMany(targetEntity=ProductSoldDiscount::class, mappedBy="productSold")
     */
    private $productSoldDiscounts;

    public function __construct()
    {
        $this->discounts = new ArrayCollection();
        $this->productSoldDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): self
    {
        $this->sale = $sale;

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

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection|Discount[]
     */
    public function getDiscounts(): Collection
    {
        return $this->discounts;
    }

    public function addDiscount(Discount $discount): self
    {
        if (!$this->discounts->contains($discount)) {
            $this->discounts[] = $discount;
            $discount->setProductSold($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): self
    {
        if ($this->discounts->contains($discount)) {
            $this->discounts->removeElement($discount);
            // set the owning side to null (unless already changed)
            if ($discount->getProductSold() === $this) {
                $discount->setProductSold(null);
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

    public function getDiscountReason(): ?string
    {
        return $this->discountReason;
    }

    public function setDiscountReason(?string $discountReason): self
    {
        $this->discountReason = $discountReason;

        return $this;
    }

    public function getDiscountId(): ?Discount
    {
        return $this->discountId;
    }

    public function setDiscountId(?Discount $discountId): self
    {
        $this->discountId = $discountId;

        return $this;
    }

    /**
     * @return Collection|ProductSoldDiscount[]
     */
    public function getProductSoldDiscounts(): Collection
    {
        return $this->productSoldDiscounts;
    }

    public function addProductSoldDiscount(ProductSoldDiscount $productSoldDiscount): self
    {
        if (!$this->productSoldDiscounts->contains($productSoldDiscount)) {
            $this->productSoldDiscounts[] = $productSoldDiscount;
            $productSoldDiscount->setProductSold($this);
        }

        return $this;
    }

    public function removeProductSoldDiscount(ProductSoldDiscount $productSoldDiscount): self
    {
        if ($this->productSoldDiscounts->removeElement($productSoldDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productSoldDiscount->getProductSold() === $this) {
                $productSoldDiscount->setProductSold(null);
            }
        }

        return $this;
    }
}
