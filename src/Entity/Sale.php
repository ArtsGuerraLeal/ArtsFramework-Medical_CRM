<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SaleRepository")
 */
class Sale
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
    private $total;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSold", mappedBy="sale")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="sale")
     */
    private $payments;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $client;

    /**
     * @ORM\Column(type="float")
     */
    private $subtotal;

    /**
     * @ORM\Column(type="float")
     */
    private $tax;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commission;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditSale", mappedBy="sale")
     */
    private $creditSales;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discount", mappedBy="sale")
     */
    private $discounts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPaid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="sales")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sales")
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->creditSales = new ArrayCollection();
        $this->discounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|ProductSold[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(ProductSold $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setSale($this);
        }

        return $this;
    }

    public function removeProduct(ProductSold $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getSale() === $this) {
                $product->setSale(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setSale($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getSale() === $this) {
                $payment->setSale(null);
            }
        }

        return $this;
    }

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(?float $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @return Collection|CreditSale[]
     */
    public function getCreditSales(): Collection
    {
        return $this->creditSales;
    }

    public function addCreditSale(CreditSale $creditSale): self
    {
        if (!$this->creditSales->contains($creditSale)) {
            $this->creditSales[] = $creditSale;
            $creditSale->setSale($this);
        }

        return $this;
    }

    public function removeCreditSale(CreditSale $creditSale): self
    {
        if ($this->creditSales->contains($creditSale)) {
            $this->creditSales->removeElement($creditSale);
            // set the owning side to null (unless already changed)
            if ($creditSale->getSale() === $this) {
                $creditSale->setSale(null);
            }
        }
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
            $discount->setSale($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): self
    {
        if ($this->discounts->contains($discount)) {
            $this->discounts->removeElement($discount);
            // set the owning side to null (unless already changed)
            if ($discount->getSale() === $this) {
                $discount->setSale(null);
            }
        }

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(?bool $isPaid): self
    {
        $this->isPaid = $isPaid;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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



}
