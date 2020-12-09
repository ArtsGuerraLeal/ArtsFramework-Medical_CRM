<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="clients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CreditSale", mappedBy="client")
     */
    private $creditSales;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="client")
     */
    private $quotes;

    public function __construct()
    {
        $this->creditSales = new ArrayCollection();
        $this->quotes = new ArrayCollection();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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
            $creditSale->setClient($this);
        }

        return $this;
    }

    public function removeCreditSale(CreditSale $creditSale): self
    {
        if ($this->creditSales->contains($creditSale)) {
            $this->creditSales->removeElement($creditSale);
            // set the owning side to null (unless already changed)
            if ($creditSale->getClient() === $this) {
                $creditSale->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Quote[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
            $quote->setClient($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getClient() === $this) {
                $quote->setClient(null);
            }
        }

        return $this;
    }
}
