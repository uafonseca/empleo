<?php

namespace App\Entity;

use App\Repository\ProfessionalSkillRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfessionalSkillRepository::class)
 * @ORM\Table
 */
class ProfessionalSkill
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
    private $porcent;

    /**
     * @ORM\ManyToOne(targetEntity=Resume::class, inversedBy="professionalSkills", cascade={"persist"})
     */
    private $resume;

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

    public function getPorcent(): ?int
    {
        return $this->porcent;
    }

    public function setPorcent(int $porcent): self
    {
        $this->porcent = $porcent;

        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

        return $this;
    }
}
