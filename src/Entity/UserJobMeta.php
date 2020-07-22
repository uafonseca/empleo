<?php

namespace App\Entity;

use App\Repository\UserJobMetaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserJobMetaRepository::class)
 * @ORM\Table
 */
class UserJobMeta
{
    const STATUS_PRESELECT = 'Preseleccionado';
    const STATUS_SELECT = 'Seleccionado';
    const STATUS_APPLIED = 'Aplicado';
    const STATUS_CANCELED = 'Cancelado';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userJobMetadata")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="userJobMetadata")
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date;

    /**
     * @var boolean
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $appiled;

    /**
     * UserJobMetadata constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_APPLIED;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    /**
     * @return bool
     */
    public function isAppiled(): ?bool
    {
        return $this->appiled;
    }

    /**
     * @param bool $appiled
     * @return UserJobMeta
     */
    public function setAppiled(?bool $appiled): self
    {
        $this->appiled = $appiled;
        return $this;
    }


}
