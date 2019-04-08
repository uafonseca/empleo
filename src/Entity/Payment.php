<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
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
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aux;

    /**
     * @ORM\Column(type="integer")
     */
    private $anouncements_number_max;

    /**
     * @ORM\Column(type="integer")
     */
    private $cv_number_max;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $adminPayment;
    /**
     * @ORM\Column(type="boolean")
     */
    private $evaluations_psicological;

    /**
     * @ORM\Column(type="integer")
     */
    private $visible_days;

    /**
     * @ORM\Column(type="integer")
     */
    private $days_importants;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selection;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAux(): ?string
    {
        return $this->aux;
    }

    public function setAux(string $aux): self
    {
        $this->aux = $aux;

        return $this;
    }

    public function getAnouncementsNumberMax(): ?int
    {
        return $this->anouncements_number_max;
    }

    public function setAnouncementsNumberMax(int $anouncements_number_max): self
    {
        $this->anouncements_number_max = $anouncements_number_max;

        return $this;
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

    public function getVisibleDays(): ?int
    {
        return $this->visible_days;
    }

    public function setVisibleDays(int $visible_days): self
    {
        $this->visible_days = $visible_days;

        return $this;
    }

    public function getDaysImportants(): ?int
    {
        return $this->days_importants;
    }

    public function setDaysImportants(int $days_importants): self
    {
        $this->days_importants = $days_importants;

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
	 * @return mixed
	 */
	public function getAdminPayment()
	{
		return $this->adminPayment;
	}
	
	/**
	 * @param mixed $adminPayment
	 */
	public function setAdminPayment($adminPayment): void
	{
		$this->adminPayment = $adminPayment;
	}
 
}
