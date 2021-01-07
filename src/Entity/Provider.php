<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProviderRepository::class)
 */
class Provider
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="providers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=ProviderOrder::class, mappedBy="provider")
     */
    private $providerOrders;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    public function __construct()
    {
        $this->providerOrders = new ArrayCollection();
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
     * @return Collection|ProviderOrder[]
     */
    public function getProviderOrders(): Collection
    {
        return $this->providerOrders;
    }

    public function addProviderOrder(ProviderOrder $providerOrder): self
    {
        if (!$this->providerOrders->contains($providerOrder)) {
            $this->providerOrders[] = $providerOrder;
            $providerOrder->setProvider($this);
        }

        return $this;
    }

    public function removeProviderOrder(ProviderOrder $providerOrder): self
    {
        if ($this->providerOrders->removeElement($providerOrder)) {
            // set the owning side to null (unless already changed)
            if ($providerOrder->getProvider() === $this) {
                $providerOrder->setProvider(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
