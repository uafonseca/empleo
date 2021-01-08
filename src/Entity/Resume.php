<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResumeRepository")
 * @Vich\Uploadable
 * @ORM\Table
 */
class Resume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="resume", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about_me;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metadata")
     */
    private $record;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metadata")
     */
    private $experience;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metadata")
     */
    private $skils;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metadata")
     */
    private $calification;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata", mappedBy="resume")
     */
    private $metadata;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $cv;
    /**
     * @Vich\UploadableField(mapping="user_cv", fileNameProperty="cv")
     * @var File
     * @Assert\File(
     *     maxSize = "2024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Solo se aceptan archivos de tipo PDF."
     * )
     */
    protected $cvFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $cart;
    /**
     * @Assert\File(
     *     maxSize = "2024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Solo se aceptan archivos de tipo PDF."
     * )
     * @Vich\UploadableField(mapping="user_cv", fileNameProperty="cart")
     * @var File
     */
    protected $cartFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Education::class, mappedBy="resume",cascade={"persist"})
     */
    private $education;

    /**
     * @ORM\OneToMany(targetEntity=Ocupation::class, mappedBy="resume", cascade={"persist"})
     */
    private $ocupations;

    /**
     * @ORM\OneToMany(targetEntity=ProfessionalSkill::class, mappedBy="resume", cascade={"persist"})
     */
    private $professionalSkills;

    /**
     * @ORM\OneToMany(targetEntity=Calification::class, mappedBy="resume", cascade={"persist"})
     */
    private $califications;


    public function __construct()
    {
        $this->metadata = new ArrayCollection();
        $this->education = new ArrayCollection();
        $this->ocupations = new ArrayCollection();
        $this->professionalSkills = new ArrayCollection();
        $this->califications = new ArrayCollection();
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

    public function getAboutMe(): ?string
    {
        return $this->about_me;
    }

    public function setAboutMe(?string $about_me): self
    {
        $this->about_me = $about_me;

        return $this;
    }

    public function getRecord(): ?Metadata
    {
        return $this->record;
    }

    public function setRecord(?Metadata $record): self
    {
        $this->record = $record;

        return $this;
    }

    public function getExperience(): ?Metadata
    {
        return $this->experience;
    }

    public function setExperience(?Metadata $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getSkils(): ?Metadata
    {
        return $this->skils;
    }

    public function setSkils(?Metadata $skils): self
    {
        $this->skils = $skils;

        return $this;
    }

    public function getCalification(): ?Metadata
    {
        return $this->calification;
    }

    public function setCalification(?Metadata $calification): self
    {
        $this->calification = $calification;

        return $this;
    }

    /**
     * @return Collection|Metadata[]
     */
    public function getMetadata(): Collection
    {
        return $this->metadata;
    }

    public function addMetadata(Metadata $metadata): self
    {
        if (!$this->metadata->contains($metadata)) {
            $this->metadata[] = $metadata;
            $metadata->setResume($this);
        }

        return $this;
    }

    public function removeMetadata(Metadata $metadata): self
    {
        if ($this->metadata->contains($metadata)) {
            $this->metadata->removeElement($metadata);
            // set the owning side to null (unless already changed)
            if ($metadata->getResume() === $this) {
                $metadata->setResume(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    /**
     * @return string
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param string $cv
     */
    public function setCv($cv): void
    {
        $this->cv = $cv;
    }

    /**
     * @return File
     */
    public function getCvFile(): ?File
    {
        return $this->cvFile;
    }

    /**
     * @param File $cvFile
     */
    public function setCvFile(File $cvFile): void
    {
        $this->cvFile= $cvFile;
        if($cvFile instanceof UploadedFile)
        {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @return string
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param string $cart
     */
    public function setCart($cart): void
    {
        $this->cart = $cart;
    }

    /**
     * @return File
     */
    public function getCartFile(): ?File
    {
        return $this->cartFile;
    }

    /**
     * @param File $cartFile
     */
    public function setCartFile(File $cartFile): void
    {
        $this->cartFile = $cartFile;
        if($cartFile instanceof UploadedFile)
        {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @return Collection|Education[]
     */
    public function getEducation(): Collection
    {
        return $this->education;
    }

    public function addEducation(Education $education): self
    {
        if (!$this->education->contains($education)) {
            $this->education[] = $education;
            $education->setResume($this);
        }

        return $this;
    }

    public function removeEducation(Education $education): self
    {
        if ($this->education->contains($education)) {
            $this->education->removeElement($education);
            // set the owning side to null (unless already changed)
            if ($education->getResume() === $this) {
                $education->setResume(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ocupation[]
     */
    public function getOcupations(): Collection
    {
        return $this->ocupations;
    }

    public function addOcupation(Ocupation $ocupation): self
    {
        if (!$this->ocupations->contains($ocupation)) {
            $this->ocupations[] = $ocupation;
            $ocupation->setResume($this);
        }

        return $this;
    }

    public function removeOcupation(Ocupation $ocupation): self
    {
        if ($this->ocupations->contains($ocupation)) {
            $this->ocupations->removeElement($ocupation);
            // set the owning side to null (unless already changed)
            if ($ocupation->getResume() === $this) {
                $ocupation->setResume(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProfessionalSkill[]
     */
    public function getProfessionalSkills(): Collection
    {
        return $this->professionalSkills;
    }

    public function addProfessionalSkill(ProfessionalSkill $professionalSkill): self
    {
        if (!$this->professionalSkills->contains($professionalSkill)) {
            $this->professionalSkills[] = $professionalSkill;
            $professionalSkill->setResume($this);
        }

        return $this;
    }

    public function removeProfessionalSkill(ProfessionalSkill $professionalSkill): self
    {
        if ($this->professionalSkills->contains($professionalSkill)) {
            $this->professionalSkills->removeElement($professionalSkill);
            // set the owning side to null (unless already changed)
            if ($professionalSkill->getResume() === $this) {
                $professionalSkill->setResume(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Calification[]
     */
    public function getCalifications(): Collection
    {
        return $this->califications;
    }

    public function addCalification(Calification $calification): self
    {
        if (!$this->califications->contains($calification)) {
            $this->califications[] = $calification;
            $calification->setResume($this);
        }

        return $this;
    }

    public function removeCalification(Calification $calification): self
    {
        if ($this->califications->contains($calification)) {
            $this->califications->removeElement($calification);
            // set the owning side to null (unless already changed)
            if ($calification->getResume() === $this) {
                $calification->setResume(null);
            }
        }

        return $this;
    }


}
