<?php

namespace App\Entity;

use App\Repository\ProductSoldDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductSoldDiscountRepository::class)
 */
class ProductSoldDiscount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProductSold::class, inversedBy="productSoldDiscounts")
     */
    private $productSold;

    /**
     * @ORM\ManyToOne(targetEntity=Discount::class, inversedBy="productSoldDiscounts")
     */
    private $discount;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="productSoldDiscounts")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Sale::class, inversedBy="productSoldDiscounts")
     */
    private $sale;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): self
    {
        $this->sale = $sale;

        return $this;
    }
}
