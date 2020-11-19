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
     * @ORM\JoinColumn(nullable=false)
     */
    private $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="discounts")
     */
    private $company;

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
}
