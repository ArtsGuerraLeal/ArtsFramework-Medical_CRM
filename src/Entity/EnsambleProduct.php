<?php

namespace App\Entity;

use App\Repository\EnsambleProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnsambleProductRepository::class)
 */
class EnsambleProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="ensambleProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Ensamble::class, inversedBy="ensambleProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ensamble;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="ensambleProducts")
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

    public function getEnsamble(): ?Ensamble
    {
        return $this->ensamble;
    }

    public function setEnsamble(?Ensamble $ensamble): self
    {
        $this->ensamble = $ensamble;

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
