<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentMethodRepository")
 */
class PaymentMethod
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="type")
     */
    private $payments;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commissionAmount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditPayment", mappedBy="type")
     */
    private $creditPayments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\company", inversedBy="paymentMethods")
     */
    private $company;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $this->creditPayments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $payment->setType($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getType() === $this) {
                $payment->setType(null);
            }
        }

        return $this;
    }

    public function getCommissionAmount(): ?float
    {
        return $this->commissionAmount;
    }

    public function setCommissionAmount(?float $commissionAmount): self
    {
        $this->commissionAmount = $commissionAmount;

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
            $creditPayment->setType($this);
        }

        return $this;
    }

    public function removeCreditPayment(CreditPayment $creditPayment): self
    {
        if ($this->creditPayments->contains($creditPayment)) {
            $this->creditPayments->removeElement($creditPayment);
            // set the owning side to null (unless already changed)
            if ($creditPayment->getType() === $this) {
                $creditPayment->setType(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCompany(): ?company
    {
        return $this->company;
    }

    public function setCompany(?company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
