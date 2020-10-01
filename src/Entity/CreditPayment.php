<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditPaymentRepository")
 */
class CreditPayment
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
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentMethod", inversedBy="creditPayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CreditSale", inversedBy="creditPayments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creditSale;

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

    public function getType(): ?PaymentMethod
    {
        return $this->type;
    }

    public function setType(?PaymentMethod $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreditSale(): ?CreditSale
    {
        return $this->creditSale;
    }

    public function setCreditSale(?CreditSale $creditSale): self
    {
        $this->creditSale = $creditSale;

        return $this;
    }
}
