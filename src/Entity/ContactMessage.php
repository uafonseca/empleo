<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactMessageRepository::class)
 * @ORM\Table
 */
class ContactMessage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name = "creador", referencedColumnName = "id", onDelete = "SET NULL")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name = "destinatario", referencedColumnName = "id", onDelete = "SET NULL")
     */
    private $destinatario;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $context;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }



    /**
     * @return User
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    /**
     * @param mixed $id
     * @return ContactMessage
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param mixed $creator
     * @return ContactMessage
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @param mixed $destinatario
     * @return ContactMessage
     */
    public function setDestinatario($destinatario)
    {
        $this->destinatario = $destinatario;
        return $this;
    }


}
