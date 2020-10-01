<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditSaleRepository")
 */
class CreditSale
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
    private $balance;

    /**
     * @ORM\Column(type="float")
     */
    private $paid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="creditSales")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale", inversedBy="creditSales")
     */
    private $sale;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditPayment", mappedBy="creditSale")
     */
    private $creditPayments;

    public function __construct()
    {
        $this->creditPayments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getPaid(): ?float
    {
        return $this->paid;
    }

    public function setPaid(float $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

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

    /**
     * @return Collection|CreditPayment[]
     */
    public function getCreditPayments(): Collection
    {
        return $this->creditPayments;
    }

    public function addCreditPayment(CreditPayment $creditPayment): self
    {
        if (!$this->creditPayments->contains($creditPayment)) {
            $this->creditPayments[] = $creditPayment;
            $creditPayment->setCreditSale($this);
        }

        return $this;
    }

    public function removeCreditPayment(CreditPayment $creditPayment): self
    {
        if ($this->creditPayments->contains($creditPayment)) {
            $this->creditPayments->removeElement($creditPayment);
            // set the owning side to null (unless already changed)
            if ($creditPayment->getCreditSale() === $this) {
                $creditPayment->setCreditSale(null);
            }
        }

        return $this;
    }
}
