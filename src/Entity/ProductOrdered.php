<?php

namespace App\Entity;

use App\Repository\ProductOrderedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductOrderedRepository::class)
 */
class ProductOrdered
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="productOrdereds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=ProviderOrder::class, inversedBy="productOrdereds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $providerOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="productOrdereds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

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

    public function getProviderOrder(): ?ProviderOrder
    {
        return $this->providerOrder;
    }

    public function setProviderOrder(?ProviderOrder $providerOrder): self
    {
        $this->providerOrder = $providerOrder;

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
