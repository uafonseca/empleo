<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfessionRepository")
 * @ORM\Table
 */
class Profession
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Anouncement", mappedBy="profession", cascade={"persist", "remove"})
     */
    private $anouncements;

    public function __construct()
    {
        $this->anouncements = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnouncements(): ?ArrayCollection
    {
        return $this->anouncements;
    }

    /**
     * @param ArrayCollection $anouncements
     */
    public function setAnouncements(ArrayCollection $anouncements): void
    {
        $this->anouncements = $anouncements;
    }


}
