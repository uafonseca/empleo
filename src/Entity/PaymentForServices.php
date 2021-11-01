<?php

namespace App\Entity;

use App\Traits\UuidEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentForServicesRepository")
 * @ORM\Table
 */
class PaymentForServices extends Payment
{

    use  UuidEntityTrait;
    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="packageServices")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=PaymentForServicesMetadata::class, mappedBy="package")
     */
    private $paymentForServicesMetadata;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $paypalCode;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->paymentForServicesMetadata = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addPackageService($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removePackageService($this);
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForServicesMetadata[]
     */
    public function getPaymentForServicesMetadata(): Collection
    {
        return $this->paymentForServicesMetadata;
    }

    public function addPaymentForServicesMetadata(PaymentForServicesMetadata $paymentForServicesMetadata): self
    {
        if (!$this->paymentForServicesMetadata->contains($paymentForServicesMetadata)) {
            $this->paymentForServicesMetadata[] = $paymentForServicesMetadata;
            $paymentForServicesMetadata->setPackage($this);
        }

        return $this;
    }

    public function removePaymentForServicesMetadata(PaymentForServicesMetadata $paymentForServicesMetadata): self
    {
        if ($this->paymentForServicesMetadata->contains($paymentForServicesMetadata)) {
            $this->paymentForServicesMetadata->removeElement($paymentForServicesMetadata);
            // set the owning side to null (unless already changed)
            if ($paymentForServicesMetadata->getPackage() === $this) {
                $paymentForServicesMetadata->setPackage(null);
            }
        }

        return $this;
    }

    public function getPaypalCode(): ?string
    {
        return $this->paypalCode;
    }

    public function setPaypalCode(?string $paypalCode): self
    {
        $this->paypalCode = $paypalCode;

        return $this;
    }

    public function __toString()
    {
        return $this->id . '';
    }
}
