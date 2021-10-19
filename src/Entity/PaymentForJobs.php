<?php

namespace App\Entity;

use App\Traits\UuidEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentForJobsRepository")
 * @ORM\Table
 */
class PaymentForJobs extends Payment
{
    use UuidEntityTrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $cv_number_max;

    /**
     * @ORM\Column(type="boolean")
     */
    private $evaluations_psicological;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selection;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="packageJobs")
     *
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PaymentForJobsMetadata", mappedBy="package", cascade={"remove"})
     */
    private $paymentForJobsMetadata;

    /**
     * Configuracion para metodo de pago PayPhone
     */

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $identificador;


    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $idClient;


    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $claveSecreta;


    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $contrasennaCodificacion;


    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $token;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->paymentForJobsMetadata = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCvNumberMax(): ?int
    {
        return $this->cv_number_max;
    }

    public function setCvNumberMax(int $cv_number_max): self
    {
        $this->cv_number_max = $cv_number_max;

        return $this;
    }

    public function getEvaluationsPsicological(): ?bool
    {
        return $this->evaluations_psicological;
    }

    public function setEvaluationsPsicological(bool $evaluations_psicological): self
    {
        $this->evaluations_psicological = $evaluations_psicological;

        return $this;
    }

    public function getSelection(): ?bool
    {
        return $this->selection;
    }

    public function setSelection(bool $selection): self
    {
        $this->selection = $selection;

        return $this;
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
            $user->addPackageJob($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removePackageJob($this);
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForJobsMetadata[]
     */
    public function getPaymentForJobsMetadata(): Collection
    {
        return $this->paymentForJobsMetadata;
    }

    public function addPaymentForJobsMetadata(PaymentForJobsMetadata $paymentForJobsMetadata): self
    {
        if (!$this->paymentForJobsMetadata->contains($paymentForJobsMetadata)) {
            $this->paymentForJobsMetadata[] = $paymentForJobsMetadata;
            $paymentForJobsMetadata->setPackage($this);
        }

        return $this;
    }

    public function removePaymentForJobsMetadata(PaymentForJobsMetadata $paymentForJobsMetadata): self
    {
        if ($this->paymentForJobsMetadata->contains($paymentForJobsMetadata)) {
            $this->paymentForJobsMetadata->removeElement($paymentForJobsMetadata);
            // set the owning side to null (unless already changed)
            if ($paymentForJobsMetadata->getPackage() === $this) {
                $paymentForJobsMetadata->setPackage(null);
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

    /**
     * Get the value of token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get the value of contrasennaCodificacion
     */
    public function getContrasennaCodificacion()
    {
        return $this->contrasennaCodificacion;
    }

    /**
     * Set the value of contrasennaCodificacion
     */
    public function setContrasennaCodificacion($contrasennaCodificacion): self
    {
        $this->contrasennaCodificacion = $contrasennaCodificacion;

        return $this;
    }

    /**
     * Get the value of claveSecreta
     */
    public function getClaveSecreta()
    {
        return $this->claveSecreta;
    }

    /**
     * Set the value of claveSecreta
     */
    public function setClaveSecreta($claveSecreta): self
    {
        $this->claveSecreta = $claveSecreta;

        return $this;
    }

    /**
     * Get the value of idClient
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set the value of idClient
     */
    public function setIdClient($idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get the value of identificador
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set the value of identificador
     */
    public function setIdentificador($identificador): self
    {
        $this->identificador = $identificador;

        return $this;
    }
}