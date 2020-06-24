<?php

namespace App\Entity;

use App\Repository\PaymentForServicesMetadataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentForServicesMetadataRepository::class)
 */
class PaymentForServicesMetadata
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paymentForServicesMetadata")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentForServices::class, inversedBy="paymentForServicesMetadata")
     */
    private $package;

    /**
     * @ORM\Column(type="integer")
     */
    private $currentPostCount;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePurchase;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPackage(): ?PaymentForServices
    {
        return $this->package;
    }

    public function setPackage(?PaymentForServices $package): self
    {
        $this->package = $package;

        return $this;
    }

    public function getCurrentPostCount(): ?int
    {
        return $this->currentPostCount;
    }

    public function setCurrentPostCount(int $currentPostCount): self
    {
        $this->currentPostCount = $currentPostCount;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDatePurchase(): ?\DateTimeInterface
    {
        return $this->datePurchase;
    }

    public function setDatePurchase(\DateTimeInterface $datePurchase): self
    {
        $this->datePurchase = $datePurchase;

        return $this;
    }
}
