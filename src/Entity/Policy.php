<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PolicyRepository")
 */
class Policy
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $welcome_message;

    /**
     * @ORM\Column(type="text")
     */
    private $privacity;

    /**
     * @ORM\Column(type="text")
     */
    private $information;

    /**
     * @ORM\Column(type="text")
     */
    private $security;

    /**
     * @ORM\Column(type="text")
     */
    private $updated;

    /**
     * @ORM\Column(type="array")
     */
    private $terms = [];

    /**
     * @ORM\Column(type="array")
     */
    private $conditions = [];

    /**
     * @ORM\Column(type="text")
     */
    private $terms_header;

    /**
     * @ORM\Column(type="text")
     */
    private $conditions_header;

    public function __construct()
    {
        $this->terms = array();
        $this->conditions= array();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWelcomeMessage(): ?string
    {
        return $this->welcome_message;
    }

    public function setWelcomeMessage(string $welcome_message): self
    {
        $this->welcome_message = $welcome_message;

        return $this;
    }

    public function getPrivacity(): ?string
    {
        return $this->privacity;
    }

    public function setPrivacity(string $privacity): self
    {
        $this->privacity = $privacity;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getSecurity(): ?string
    {
        return $this->security;
    }

    public function setSecurity(string $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function getUpdated(): ?string
    {
        return $this->updated;
    }

    public function setUpdated(string $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getTerms(): ?array
    {
        return $this->terms;
    }

    public function setTerms(array $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function getConditions(): ?array
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getTermsHeader(): ?string
    {
        return $this->terms_header;
    }

    public function setTermsHeader(string $terms_header): self
    {
        $this->terms_header = $terms_header;

        return $this;
    }

    public function getConditionsHeader(): ?string
    {
        return $this->conditions_header;
    }

    public function setConditionsHeader(string $conditions_header): self
    {
        $this->conditions_header = $conditions_header;

        return $this;
    }
}