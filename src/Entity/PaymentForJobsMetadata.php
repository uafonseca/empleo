<?php

namespace App\Entity;

use App\Repository\PaymentForJobsMetadataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PaymentForJobsMetadataRepository::class)
 * @ORM\Table
 * @Gedmo\Loggable
 */
class PaymentForJobsMetadata
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="paymentForJobsMetadata")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PaymentForJobs", inversedBy="paymentForJobsMetadata")
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

    /**
     * @ORM\Column(type="string")
     */
    private $transaccion;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPackage(): ?PaymentForJobs
    {
        return $this->package;
    }

    public function setPackage(?PaymentForJobs $package): self
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

    public function getDatePurchase(): ?\DateTime
    {
        return $this->datePurchase;
    }

    public function setDatePurchase(\DateTimeInterface $datePurchase): self
    {
        $this->datePurchase = $datePurchase;

        return $this;
    }

    /**
     * Get the value of transaccion
     */
    public function getTransaccion()
    {
        return $this->transaccion;
    }

    /**
     * Set the value of transaccion
     */
    public function setTransaccion($transaccion): self
    {
        $this->transaccion = $transaccion;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}