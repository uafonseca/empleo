<?php

namespace App\Entity;

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
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $title;
    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="Category")
     *
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $localtion;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $experience;

    /**
     * @Assert\Type("integer")
     * @Assert\Expression("this.getSalaryMax() >= this.getSalaryMin()")
     * @ORM\Column(type="integer")
     */
    private $salary_max;

    /**
     * @Assert\Type("integer")
     * @Assert\Expression("this.getSalaryMin() <= this.getSalaryMax()")
     * @ORM\Column(type="integer")
     */
    private $salary_min;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo responsabilidades debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo responsabilidades no debe superar los {{ limit }} caracteres"
     * )
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $responsabilities;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo educación debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo educación no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $education;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo otros debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo otros no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255)
     */
    private $your_localtion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company_name;

    /**
     * @Assert\Url(relativeProtocol = true)
     * @ORM\Column(type="string", length=255)
     */
    private $web_address;

    /**
     *  @Assert\Length(
     *      min = 10,
     *      max = 250,
     *      minMessage = "El campo Acerca de la compañía debe tener más de {{ limit }} caracteres",
     *      maxMessage = "El campo Acerca de la compañía no debe superar los {{ limit }} caracteres"
     * )
     * @ORM\Column(type="string", length=255)
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
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function __construct()
    {
        $this->applications = array();
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
    public function setImage(string $image): void
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

}
