<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceJobRepository")
 * @ORM\Table
 */
class Service
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
     * @ORM\OneToMany(targetEntity="App\Entity\Job", mappedBy="service")
     */
    private $jobs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Anouncement", mappedBy="profession")
     */
    private $anouncements;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
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

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setService($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
            // set the owning side to null (unless already changed)
            if ($job->getService() === $this) {
                $job->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Anouncement[]
     */
    public function getAnouncements(): Collection
    {
        return $this->anouncements;
    }

    public function addAnouncement(Anouncement $anouncement): self
    {
        if (!$this->anouncements->contains($anouncement)) {
            $this->anouncements[] = $anouncement;
            $anouncement->setProfession($this);
        }

        return $this;
    }

    public function removeAnouncement(Anouncement $anouncement): self
    {
        if ($this->anouncements->contains($anouncement)) {
            $this->anouncements->removeElement($anouncement);
            // set the owning side to null (unless already changed)
            if ($anouncement->getProfession() === $this) {
                $anouncement->setProfession(null);
            }
        }

        return $this;
    }
}
