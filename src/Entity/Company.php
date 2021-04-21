<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
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
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User",mappedBy="company",cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Treatment",mappedBy="company",cascade={"persist"})
     */
    private $treatment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Equipment",mappedBy="company",cascade={"persist"})
     */
    private $equipment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Patient",mappedBy="company",cascade={"persist"})
     */
    private $patient;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Appointment",mappedBy="company",cascade={"persist"})
     */
    private $appointment;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Address",mappedBy="company",cascade={"persist"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomData", mappedBy="company")
     */
    private $customData;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Staff", mappedBy="company", orphanRemoval=true)
     */
    private $staff;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StaffPositions", mappedBy="company")
     */
    private $staffPositions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CustomForm", mappedBy="company")
     */
    private $customForms;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $settings = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $googleJson = [];

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imagePath;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPaid;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $paymentDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $paymentExpiration;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $StripeId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Client", mappedBy="company")
     */
    private $clients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale", mappedBy="company")
     */
    private $sales;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="company")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSold", mappedBy="company")
     */
    private $productSolds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="company")
     */
    private $payments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Discount", mappedBy="company")
     */
    private $discounts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentMethod", mappedBy="company")
     */
    private $paymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="company")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="company")
     */
    private $quotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductQuote", mappedBy="company")
     */
    private $productQuotes;

    /**
     * @ORM\OneToMany(targetEntity=Provider::class, mappedBy="company")
     */
    private $providers;

    /**
     * @ORM\OneToMany(targetEntity=ProviderOrder::class, mappedBy="company")
     */
    private $providerOrders;

    /**
     * @ORM\OneToMany(targetEntity=ProductOrdered::class, mappedBy="company")
     */
    private $productOrdereds;

    /**
     * @ORM\OneToMany(targetEntity=ProductSoldDiscount::class, mappedBy="company")
     */
    private $productSoldDiscounts;

    /**
     * @ORM\OneToMany(targetEntity=ProductStock::class, mappedBy="company")
     */
    private $productStocks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $line2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postalcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $logotype;

    /**
     * @ORM\OneToMany(targetEntity=ProductQuoteDiscount::class, mappedBy="company")
     */
    private $productQuoteDiscounts;

    /**
     * @ORM\OneToMany(targetEntity=Vendor::class, mappedBy="company")
     */
    private $vendors;



    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->treatment = new ArrayCollection();
        $this->equipment = new ArrayCollection();
        $this->patient = new ArrayCollection();
        $this->appointment = new ArrayCollection();
        $this->address = new ArrayCollection();
        $this->customData = new ArrayCollection();
        $this->staff = new ArrayCollection();
        $this->staffPositions = new ArrayCollection();
        $this->customForms = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->productSolds = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->discounts = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->quotes = new ArrayCollection();
        $this->productQuotes = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->providerOrders = new ArrayCollection();
        $this->productOrdereds = new ArrayCollection();
        $this->productSoldDiscounts = new ArrayCollection();
        $this->productStocks = new ArrayCollection();
        $this->productQuoteDiscounts = new ArrayCollection();
        $this->vendors = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setCompany($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->user->contains($user)) {
            $this->user->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Treatment[]
     */
    public function getTreatment(): Collection
    {
        return $this->treatment;
    }

    public function addTreatment(Treatment $treatment): self
    {
        if (!$this->treatment->contains($treatment)) {
            $this->treatment[] = $treatment;
            $treatment->setCompany($this);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): self
    {
        if ($this->treatment->contains($treatment)) {
            $this->treatment->removeElement($treatment);
            // set the owning side to null (unless already changed)
            if ($treatment->getCompany() === $this) {
                $treatment->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Equipment[]
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
            $equipment->setCompany($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->contains($equipment)) {
            $this->equipment->removeElement($equipment);
            // set the owning side to null (unless already changed)
            if ($equipment->getCompany() === $this) {
                $equipment->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Patient[]
     */
    public function getPatient(): Collection
    {
        return $this->patient;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patient->contains($patient)) {
            $this->patient[] = $patient;
            $patient->setCompany($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patient->contains($patient)) {
            $this->patient->removeElement($patient);
            // set the owning side to null (unless already changed)
            if ($patient->getCompany() === $this) {
                $patient->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointment(): Collection
    {
        return $this->appointment;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointment->contains($appointment)) {
            $this->appointment[] = $appointment;
            $appointment->setCompany($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointment->contains($appointment)) {
            $this->appointment->removeElement($appointment);
            // set the owning side to null (unless already changed)
            if ($appointment->getCompany() === $this) {
                $appointment->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Address[]
     */
    public function getAddress(): Collection
    {
        return $this->address;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->address->contains($address)) {
            $this->address[] = $address;
            $address->setCompany($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->address->contains($address)) {
            $this->address->removeElement($address);
            // set the owning side to null (unless already changed)
            if ($address->getCompany() === $this) {
                $address->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomData[]
     */
    public function getCustomData(): Collection
    {
        return $this->customData;
    }

    public function addCustomData(CustomData $customData): self
    {
        if (!$this->customData->contains($customData)) {
            $this->customData[] = $customData;
            $customData->setCompany($this);
        }

        return $this;
    }

    public function removeCustomData(CustomData $customData): self
    {
        if ($this->customData->contains($customData)) {
            $this->customData->removeElement($customData);
            // set the owning side to null (unless already changed)
            if ($customData->getCompany() === $this) {
                $customData->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
       return $this->name;
    }

    /**
     * @return Collection|Staff[]
     */
    public function getStaff(): Collection
    {
        return $this->staff;
    }

    public function addStaff(Staff $staff): self
    {
        if (!$this->staff->contains($staff)) {
            $this->staff[] = $staff;
            $staff->setCompany($this);
        }

        return $this;
    }

    public function removeStaff(Staff $staff): self
    {
        if ($this->staff->contains($staff)) {
            $this->staff->removeElement($staff);
            // set the owning side to null (unless already changed)
            if ($staff->getCompany() === $this) {
                $staff->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StaffPositions[]
     */
    public function getStaffPositions(): Collection
    {
        return $this->staffPositions;
    }

    public function addStaffPosition(StaffPositions $staffPosition): self
    {
        if (!$this->staffPositions->contains($staffPosition)) {
            $this->staffPositions[] = $staffPosition;
            $staffPosition->setCompany($this);
        }

        return $this;
    }

    public function removeStaffPosition(StaffPositions $staffPosition): self
    {
        if ($this->staffPositions->contains($staffPosition)) {
            $this->staffPositions->removeElement($staffPosition);
            // set the owning side to null (unless already changed)
            if ($staffPosition->getCompany() === $this) {
                $staffPosition->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CustomForm[]
     */
    public function getCustomForms(): Collection
    {
        return $this->customForms;
    }

    public function addCustomForm(CustomForm $customForm): self
    {
        if (!$this->customForms->contains($customForm)) {
            $this->customForms[] = $customForm;
            $customForm->setCompany($this);
        }

        return $this;
    }

    public function removeCustomForm(CustomForm $customForm): self
    {
        if ($this->customForms->contains($customForm)) {
            $this->customForms->removeElement($customForm);
            // set the owning side to null (unless already changed)
            if ($customForm->getCompany() === $this) {
                $customForm->setCompany(null);
            }
        }

        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function getGoogleJson(): ?array
    {
        return $this->googleJson;
    }

    public function setGoogleJson(?array $googleJson): self
    {
        $this->googleJson = $googleJson;

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

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

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

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getPaymentExpiration(): ?\DateTimeInterface
    {
        return $this->paymentExpiration;
    }

    public function setPaymentExpiration(?\DateTimeInterface $paymentExpiration): self
    {
        $this->paymentExpiration = $paymentExpiration;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->StripeId;
    }

    public function setStripeId(?string $StripeId): self
    {
        $this->StripeId = $StripeId;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setCompany($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getCompany() === $this) {
                $client->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sale[]
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales[] = $sale;
            $sale->setCompany($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->contains($sale)) {
            $this->sales->removeElement($sale);
            // set the owning side to null (unless already changed)
            if ($sale->getCompany() === $this) {
                $sale->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCompany($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCompany() === $this) {
                $product->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductSold[]
     */
    public function getProductSolds(): Collection
    {
        return $this->productSolds;
    }

    public function addProductSold(ProductSold $productSold): self
    {
        if (!$this->productSolds->contains($productSold)) {
            $this->productSolds[] = $productSold;
            $productSold->setCompany($this);
        }

        return $this;
    }

    public function removeProductSold(ProductSold $productSold): self
    {
        if ($this->productSolds->contains($productSold)) {
            $this->productSolds->removeElement($productSold);
            // set the owning side to null (unless already changed)
            if ($productSold->getCompany() === $this) {
                $productSold->setCompany(null);
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
            $payment->setCompany($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getCompany() === $this) {
                $payment->setCompany(null);
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
            $discount->setCompany($this);
        }

        return $this;
    }

    public function removeDiscount(Discount $discount): self
    {
        if ($this->discounts->contains($discount)) {
            $this->discounts->removeElement($discount);
            // set the owning side to null (unless already changed)
            if ($discount->getCompany() === $this) {
                $discount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentMethod[]
     */
    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): self
    {
        if (!$this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods[] = $paymentMethod;
            $paymentMethod->setCompany($this);
        }

        return $this;
    }

    public function removePaymentMethod(PaymentMethod $paymentMethod): self
    {
        if ($this->paymentMethods->contains($paymentMethod)) {
            $this->paymentMethods->removeElement($paymentMethod);
            // set the owning side to null (unless already changed)
            if ($paymentMethod->getCompany() === $this) {
                $paymentMethod->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setCompany($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getCompany() === $this) {
                $category->setCompany(null);
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
            $quote->setCompany($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getCompany() === $this) {
                $quote->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductQuote[]
     */
    public function getProductQuotes(): Collection
    {
        return $this->productQuotes;
    }

    public function addProductQuote(ProductQuote $productQuote): self
    {
        if (!$this->productQuotes->contains($productQuote)) {
            $this->productQuotes[] = $productQuote;
            $productQuote->setCompany($this);
        }

        return $this;
    }

    public function removeProductQuote(ProductQuote $productQuote): self
    {
        if ($this->productQuotes->contains($productQuote)) {
            $this->productQuotes->removeElement($productQuote);
            // set the owning side to null (unless already changed)
            if ($productQuote->getCompany() === $this) {
                $productQuote->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Provider[]
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers[] = $provider;
            $provider->setCompany($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getCompany() === $this) {
                $provider->setCompany(null);
            }
        }

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
            $providerOrder->setCompany($this);
        }

        return $this;
    }

    public function removeProviderOrder(ProviderOrder $providerOrder): self
    {
        if ($this->providerOrders->removeElement($providerOrder)) {
            // set the owning side to null (unless already changed)
            if ($providerOrder->getCompany() === $this) {
                $providerOrder->setCompany(null);
            }
        }

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
            $productOrdered->setCompany($this);
        }

        return $this;
    }

    public function removeProductOrdered(ProductOrdered $productOrdered): self
    {
        if ($this->productOrdereds->removeElement($productOrdered)) {
            // set the owning side to null (unless already changed)
            if ($productOrdered->getCompany() === $this) {
                $productOrdered->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductSoldDiscount[]
     */
    public function getProductSoldDiscounts(): Collection
    {
        return $this->productSoldDiscounts;
    }

    public function addProductSoldDiscount(ProductSoldDiscount $productSoldDiscount): self
    {
        if (!$this->productSoldDiscounts->contains($productSoldDiscount)) {
            $this->productSoldDiscounts[] = $productSoldDiscount;
            $productSoldDiscount->setCompany($this);
        }

        return $this;
    }

    public function removeProductSoldDiscount(ProductSoldDiscount $productSoldDiscount): self
    {
        if ($this->productSoldDiscounts->removeElement($productSoldDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productSoldDiscount->getCompany() === $this) {
                $productSoldDiscount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductStock[]
     */
    public function getProductStocks(): Collection
    {
        return $this->productStocks;
    }

    public function addProductStock(ProductStock $productStock): self
    {
        if (!$this->productStocks->contains($productStock)) {
            $this->productStocks[] = $productStock;
            $productStock->setCompany($this);
        }

        return $this;
    }

    public function removeProductStock(ProductStock $productStock): self
    {
        if ($this->productStocks->removeElement($productStock)) {
            // set the owning side to null (unless already changed)
            if ($productStock->getCompany() === $this) {
                $productStock->setCompany(null);
            }
        }

        return $this;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(?string $line1): self
    {
        $this->line1 = $line1;

        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(?string $line2): self
    {
        $this->line2 = $line2;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(?string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone1(): ?string
    {
        return $this->phone1;
    }

    public function setPhone1(?string $phone1): self
    {
        $this->phone1 = $phone1;

        return $this;
    }

    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    public function setPhone2(?string $phone2): self
    {
        $this->phone2 = $phone2;

        return $this;
    }

    public function getLegal(): ?string
    {
        return $this->legal;
    }

    public function setLegal(?string $legal): self
    {
        $this->legal = $legal;

        return $this;
    }

    public function getLogotype(): ?int
    {
        return $this->logotype;
    }

    public function setLogotype(?int $logotype): self
    {
        $this->logotype = $logotype;

        return $this;
    }

    /**
     * @return Collection|ProductQuoteDiscount[]
     */
    public function getProductQuoteDiscounts(): Collection
    {
        return $this->productQuoteDiscounts;
    }

    public function addProductQuoteDiscount(ProductQuoteDiscount $productQuoteDiscount): self
    {
        if (!$this->productQuoteDiscounts->contains($productQuoteDiscount)) {
            $this->productQuoteDiscounts[] = $productQuoteDiscount;
            $productQuoteDiscount->setCompany($this);
        }

        return $this;
    }

    public function removeProductQuoteDiscount(ProductQuoteDiscount $productQuoteDiscount): self
    {
        if ($this->productQuoteDiscounts->removeElement($productQuoteDiscount)) {
            // set the owning side to null (unless already changed)
            if ($productQuoteDiscount->getCompany() === $this) {
                $productQuoteDiscount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vendor[]
     */
    public function getVendors(): Collection
    {
        return $this->vendors;
    }

    public function addVendor(Vendor $vendor): self
    {
        if (!$this->vendors->contains($vendor)) {
            $this->vendors[] = $vendor;
            $vendor->setCompany($this);
        }

        return $this;
    }

    public function removeVendor(Vendor $vendor): self
    {
        if ($this->vendors->removeElement($vendor)) {
            // set the owning side to null (unless already changed)
            if ($vendor->getCompany() === $this) {
                $vendor->setCompany(null);
            }
        }

        return $this;
    }

    

  

}
