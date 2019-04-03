<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
     * @ORM\Column(type="string", length=255)
     */
    private $ico;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="category")
     */
    private $users_list;

    public function __construct()
    {
        $this->users_list = new ArrayCollection();
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

    public function getIco(): ?string
    {
        return $this->ico;
    }

    public function setIco(string $ico): self
    {
        $this->ico = $ico;

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

    public function __toString()
    {
       return $this->getName();
    }

    /**
     * @return Collection|User[]
     */
    public function getUsersList(): Collection
    {
        return $this->users_list;
    }

    public function addUsersList(User $usersList): self
    {
        if (!$this->users_list->contains($usersList)) {
            $this->users_list[] = $usersList;
            $usersList->addCategory($this);
        }

        return $this;
    }

    public function removeUsersList(User $usersList): self
    {
        if ($this->users_list->contains($usersList)) {
            $this->users_list->removeElement($usersList);
            $usersList->removeCategory($this);
        }

        return $this;
    }
}
