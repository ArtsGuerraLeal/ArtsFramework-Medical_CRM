<?php

namespace App\Entity;

use App\Repository\ProductQuoteDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductQuoteDiscountRepository::class)
 */
class ProductQuoteDiscount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProductQuote::class, inversedBy="productQuoteDiscounts")
     */
    private $productquote;

    /**
     * @ORM\ManyToOne(targetEntity=Discount::class, inversedBy="productQuoteDiscounts")
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="productQuoteDiscounts")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Quote::class, inversedBy="productQuoteDiscounts")
     */
    private $quote;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductquote(): ?ProductQuote
    {
        return $this->productquote;
    }

    public function setProductquote(?ProductQuote $productquote): self
    {
        $this->productquote = $productquote;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
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

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): self
    {
        $this->quote = $quote;

        return $this;
    }
}
