<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 * @Vich\Uploadable
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Campo requerido")
     * @ORM\Column(type="string", length=255)
     */
    private $title;
    /**
     * @Assert\NotBlank(message="Campo requerido")
     * @ORM\ManyToOne(targetEntity="Category",cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $localtion;

    /**
     * @Assert\NotBlank(message="Campo requerido")
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @Assert\NotBlank(message="Campo requerido")
     * @ORM\Column(type="string", length=255)
     */
    private $experience;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salary_max;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     */
    private $salary_min;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $qualification;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo descripción debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo descripción no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="text", length=255)
     */
    private $description;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 900,
     *      minMessage = "El campo responsabilidades debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo responsabilidades no debe superar los {{ limit }} caracteres"
     * )
     * )
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $responsabilities;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 900,
     *      minMessage = "El campo educación debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo educación no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255 ,nullable=true)
     */
    private $education;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 900,
     *      minMessage = "El campo otros debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo otros no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $others;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $your_localtion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $company_name;

    /**
     * @Assert\Url(relativeProtocol = true)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web_address;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo Acerca de la compañía debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo Acerca de la compañía no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $campany_profile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */

    protected $image;

    /**
     * @Assert\Image(
     *     allowLandscape = true,
     *     allowPortrait = true
     * )
     * @Vich\UploadableField(mapping="company_images", fileNameProperty="image")
     * @var File
     */
    protected $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     *
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiredDate;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $applications = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="jobAppiled",cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $users;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="jobs")
     */
    private $service;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $video_link;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="jobs",cascade={"persist"})
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=UserJobMetadata::class, mappedBy="job")
     */
    private $userJobMetadata;

    public function __construct()
    {
        $this->applications = array();
        $this->users = new ArrayCollection();
        $this->userJobMetadata = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param mixed $gender
     */
    public function setGender($gender): void
    {
        $this->gender = $gender;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getLocaltion(): ?string
    {
        return $this->localtion;
    }

    public function setLocaltion(string $localtion): self
    {
        $this->localtion = $localtion;

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

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getSalaryMax(): ?int
    {
        return $this->salary_max;
    }

    public function setSalaryMax(int $salary_max): self
    {
        $this->salary_max = $salary_max;

        return $this;
    }

    public function getSalaryMin(): ?int
    {
        return $this->salary_min;
    }

    public function setSalaryMin(int $salary_min): self
    {
        $this->salary_min = $salary_min;

        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setQualification(string $qualification): self
    {
        $this->qualification = $qualification;

        return $this;
    }


    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

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

    public function getResponsabilities(): ?string
    {
        return $this->responsabilities;
    }

    public function setResponsabilities(string $responsabilities): self
    {
        $this->responsabilities = $responsabilities;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(string $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getOthers(): ?string
    {
        return $this->others;
    }

    public function setOthers(string $others): self
    {
        $this->others = $others;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getYourLocaltion(): ?string
    {
        return $this->your_localtion;
    }

    public function setYourLocaltion(string $your_localtion): self
    {
        $this->your_localtion = $your_localtion;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(string $company_name): self
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getWebAddress(): ?string
    {
        return $this->web_address;
    }

    public function setWebAddress(string $web_address): self
    {
        $this->web_address = $web_address;

        return $this;
    }

    public function getCampanyProfile(): ?string
    {
        return $this->campany_profile;
    }

    public function setCampanyProfile(string $campany_profile): self
    {
        $this->campany_profile = $campany_profile;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }


    public function getImageFile()
    {
        return $this->imageFile;
    }


    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @param mixed $applications
     */
    public function setApplications(?string $applications): void
    {
        $this->applications[] = $applications;
    }
    public function removeApplication($applied)
    {
        unset($this->applications[$applied]);
        $this->applications = array_values($this->applications);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param mixed $expiredDate
     */
    public function setExpiredDate($expiredDate): void
    {
        $this->expiredDate = $expiredDate;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addJobAppiled($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeJobAppiled($this);
        }

        return $this;
    }

//    public function getIsService(): ?bool
//    {
//        return $this->is_service;
//    }
//
//    public function setIsService(bool $is_service): self
//    {
//        $this->is_service = $is_service;
//
//        return $this;
//    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getVideoLink(): ?string
    {
        return $this->video_link;
    }

    public function setVideoLink(?string $video_link): self
    {
        $this->video_link = $video_link;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|UserJobMetadata[]
     */
    public function getUserJobMetadata(): Collection
    {
        return $this->userJobMetadata;
    }

    public function addUserJobMetadata(UserJobMetadata $userJobMetadata): self
    {
        if (!$this->userJobMetadata->contains($userJobMetadata)) {
            $this->userJobMetadata[] = $userJobMetadata;
            $userJobMetadata->setJob($this);
        }

        return $this;
    }

    public function removeUserJobMetadata(UserJobMetadata $userJobMetadata): self
    {
        if ($this->userJobMetadata->contains($userJobMetadata)) {
            $this->userJobMetadata->removeElement($userJobMetadata);
            // set the owning side to null (unless already changed)
            if ($userJobMetadata->getJob() === $this) {
                $userJobMetadata->setJob(null);
            }
        }

        return $this;
    }

}
