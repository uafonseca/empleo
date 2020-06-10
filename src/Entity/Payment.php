<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass() */
abstract class Payment
{
	
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
    private $visible_days;

    /**
     * @ORM\Column(type="integer")
     */
    private $days_importants;

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
}
