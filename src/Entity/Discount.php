<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRepository")
 */
class Discount
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductSold", inversedBy="discounts")
     */
    private $productSold;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale", inversedBy="discounts")
     */
    private $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="discounts")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quote", inversedBy="discounts")
     */
    private $quote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductQuote", inversedBy="discounts")
     */
    private $productQuote;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=ProductSold::class, mappedBy="discountId")
     */
    private $productSolds;

    /**
     * @ORM\OneToMany(targetEntity=ProductSoldDiscount::class, mappedBy="discount")
     */
    private $productSoldDiscounts;

    /**
     * @ORM\OneToMany(targetEntity=ProductQuoteDiscount::class, mappedBy="discount")
     */
    private $productQuoteDiscounts;

    public function __construct()
    {
        $this->productSolds = new ArrayCollection();
        $this->productSoldDiscounts = new ArrayCollection();
        $this->productQuoteDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getProductSold(): ?ProductSold
    {
        return $this->productSold;
    }

    public function setProductSold(?ProductSold $productSold): self
    {
        $this->productSold = $productSold;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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

    public function getProductQuote(): ?ProductQuote
    {
        return $this->productQuote;
    }

    public function setProductQuote(?ProductQuote $productQuote): self
    {
        $this->productQuote = $productQuote;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

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
            $productSold->setDiscountId($this);
        }

        return $this;
    }

    public function removeProductSold(ProductSold $productSold): self
    {
        if ($this->productSolds->removeElement($productSold)) {
            // set the owning side to null (unless already changed)
            if ($productSold->getDiscountId() === $this) {
                $productSold->setDiscountId(null);
            }
        }

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
            $productSoldDiscount->setDiscount($this);
        }

        return $this;
    }

    public function removeProductSoldDiscount(ProductSoldDiscount $productSoldDiscount): self
    {
        if ($this->productSoldDiscounts->removeElement($productSoldDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productSoldDiscount->getDiscount() === $this) {
                $productSoldDiscount->setDiscount(null);
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
            $productQuoteDiscount->setDiscount($this);
        }

        return $this;
    }

    public function removeProductQuoteDiscount(ProductQuoteDiscount $productQuoteDiscount): self
    {
        if ($this->productQuoteDiscounts->removeElement($productQuoteDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productQuoteDiscount->getDiscount() === $this) {
                $productQuoteDiscount->setDiscount(null);
            }
        }

        return $this;
    }
}
