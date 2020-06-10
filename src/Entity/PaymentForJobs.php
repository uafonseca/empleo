<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentForJobsRepository")
 */
class PaymentForJobs extends Payment
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
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
}
