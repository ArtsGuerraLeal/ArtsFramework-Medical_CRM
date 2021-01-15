<?php

namespace App\Entity;

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
}
