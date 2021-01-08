<?php

namespace App\Entity;

use App\Repository\StaticPageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=StaticPageRepository::class)
 * @UniqueEntity(fields={"type"}, message="EL tipo seleccionado ya esta en uso.")
 * @ORM\Table
 */
class StaticPage
{

    public const TYPE_ABOUT = 'Sobre nosotros';
    public const TYPE_CONTACT = 'Contacto';
    public const TYPE_PRICE = 'Determinación de precios';
    public const TYPE_HOW_WORKING = 'Como funciona';
    public const TYPE_FAQ = 'FAQ';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $updateAt;

    /**
     * @ORM\Column(type="text")
     */
    private $context;

    /**
     *
     * @ORM\Column(type="string", length=150, unique=true)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
