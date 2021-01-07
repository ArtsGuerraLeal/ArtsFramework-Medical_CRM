<?php

namespace App\Entity;

use App\Repository\ProviderOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProviderOrderRepository::class)
 */
class ProviderOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $client;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="providerOrders")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="providerOrders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $provider;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrdered::class, mappedBy="providerOrder")
     */
    private $productOrdereds;

    public function __construct()
    {
        $this->productOrdereds = new ArrayCollection();
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

    public function getClient(): ?string
    {
        return $this->client;
    }

    public function setClient(string $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Collection|ProductOrdered[]
     */
    public function getProductOrdereds(): Collection
    {
        return $this->productOrdereds;
    }

    public function addProductOrdered(ProductOrdered $productOrdered): self
    {
        if (!$this->productOrdereds->contains($productOrdered)) {
            $this->productOrdereds[] = $productOrdered;
            $productOrdered->setProviderOrder($this);
        }

        return $this;
    }

    public function removeProductOrdered(ProductOrdered $productOrdered): self
    {
        if ($this->productOrdereds->removeElement($productOrdered)) {
            // set the owning side to null (unless already changed)
            if ($productOrdered->getProviderOrder() === $this) {
                $productOrdered->setProviderOrder(null);
            }
        }

        return $this;
    }
}
