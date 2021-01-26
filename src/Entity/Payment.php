<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="type_idx", columns={"type"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=2)
 * @ORM\DiscriminatorMap({
 *     "j"="PaymentForJobs",
 *     "s"="PaymentForServices"
 * })
 */
abstract class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $aux;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Payment
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }



    /**
     * @ORM\Column(type="integer")
     */
    public $anouncements_number_max;
    
    /**
     * @ORM\Column(type="integer")
     */
    public  $visible_days;

    /**
     * @ORM\Column(type="integer")
     */
    protected $days_importants;


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
