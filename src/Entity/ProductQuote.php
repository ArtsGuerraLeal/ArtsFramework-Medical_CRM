<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductQuoteRepository")
 */
class ProductQuote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productQuotes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quote", inversedBy="productQuotes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $quote;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="productQuotes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discount", mappedBy="productQuote")
     */
    private $discounts;

    /**
     * @ORM\OneToMany(targetEntity=ProductQuoteDiscount::class, mappedBy="productquote")
     */
    private $productQuoteDiscounts;

    public function __construct()
    {
        $this->discounts = new ArrayCollection();
        $this->productQuoteDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): self
    {
        $this->quote = $quote;

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

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;

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
            $discount->setProductQuote($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): self
    {
        if ($this->discounts->contains($discount)) {
            $this->discounts->removeElement($discount);
            // set the owning side to null (unless already changed)
            if ($discount->getProductQuote() === $this) {
                $discount->setProductQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductQuoteDiscount[]
     */
    public function getProductQuoteDiscounts(): Collection
    {
        return $this->productQuoteDiscounts;
    }

    public function addProductQuoteDiscount(ProductQuoteDiscount $productQuoteDiscount): self
    {
        if (!$this->productQuoteDiscounts->contains($productQuoteDiscount)) {
            $this->productQuoteDiscounts[] = $productQuoteDiscount;
            $productQuoteDiscount->setProductquote($this);
        }

        return $this;
    }

    public function removeProductQuoteDiscount(ProductQuoteDiscount $productQuoteDiscount): self
    {
        if ($this->productQuoteDiscounts->removeElement($productQuoteDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productQuoteDiscount->getProductquote() === $this) {
                $productQuoteDiscount->setProductquote(null);
            }
        }

        return $this;
    }
}
