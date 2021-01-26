<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 2/11/2019
 * Time: 12:00 PM
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @Vich\Uploadable
 * @UniqueEntity(fields={"email"}, message="La dirección de correo proporcionada ya está en uso")
 * @UniqueEntity(fields={"username"}, message="El nombre de usuario proporcionado ya está en uso")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $name;


    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    protected $employer;

    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    protected $buyFreePackService;

    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    protected $candidate;
    /**
     * @ORM\Column(type="boolean", length=100, nullable=true)
     */
    protected $buyFreePackJob;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $image;

    /**
     * @Vich\UploadableField(mapping="user_images", fileNameProperty="image")
     * @var File
     */
    protected $imageFile;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $companyName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $address;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $about;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $videoIntro;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $social_facebook;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $social_twitter;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $social_google;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resume", mappedBy="user",cascade={"persist", "remove"} )
     */
    private $resume;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Title")
     */
    private $higherLevelTitlee;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Profession")
     */
    private $profession;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $experience;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResumeMetadata", mappedBy="user",cascade={"remove", "persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $skils;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $social_links = [];


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $bookmarked = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $applied = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Job", inversedBy="users",cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     *
     */
    private $jobAppiled;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="users_list",cascade={"persist"})
     */
    private $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $companiesSeen;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $secret;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : false})
     */
    private $verificated;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Anouncement", mappedBy="user",cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $anouncements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user",cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $notifications;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_of_purchase;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_of_purchase_service;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num_posts;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $num_posts_services;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $servicesRequest = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $candidates = [];

    /**
     * @ORM\OneToOne(targetEntity=Company::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $company;

    /**
     * @ORM\ManyToMany(targetEntity=PaymentForJobs::class, inversedBy="users")
     */
    private $packageJobs;

    /**
     * @ORM\ManyToMany(targetEntity=PaymentForServices::class, inversedBy="users")
     */
    private $packageServices;

    /**
     * @ORM\OneToMany(targetEntity=PaymentForJobsMetadata::class, mappedBy="user")
     */
    private $paymentForJobsMetadata;

    /**
     * @ORM\OneToMany(targetEntity=PaymentForServicesMetadata::class, mappedBy="user")
     */
    private $paymentForServicesMetadata;

    /**
     * @ORM\OneToMany(targetEntity=UserJobMeta::class, mappedBy="user")
     */
    private $userJobMetadata;

    public function __construct()
    {
        parent::__construct();
        $this->skils = new ArrayCollection();
        $this->social_links = array();
        $this->bookmarked = array();
        $this->applied = array();
        $this->jobAppiled = new ArrayCollection();
        $this->servicesRequest = array();
        $this->candidates = array();
        if ($this->candidate) {
            $this->setResume(new Resume());
        }
        $this->category = new ArrayCollection();
        $this->companiesSeen = 0;
        $this->anouncements = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->packageJobs = new ArrayCollection();
        $this->packageServices = new ArrayCollection();
        $this->paymentForJobsMetadata = new ArrayCollection();
        $this->paymentForServicesMetadata = new ArrayCollection();
        $this->userJobMetadata = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if ($imageFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
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
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getCandidate()
    {
        return $this->candidate;
    }

    /**
     * @param mixed $candidate
     */
    public function setCandidate($candidate): void
    {
        $this->candidate = $candidate;
    }

    /**
     * @return mixed
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * @param mixed $employer
     */
    public function setEmployer($employer): void
    {
        $this->employer = $employer;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param string $about
     */
    public function setAbout($about): void
    {
        if ($about == null) {
            $about = "";
        }
        $this->about = $about;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getVideoIntro()
    {
        return $this->videoIntro;
    }

    /**
     * @param string $videoIntro
     */
    public function setVideoIntro(string $videoIntro): void
    {
        $this->videoIntro = $videoIntro;
    }

    /**
     * @return string
     */
    public function getSocialFacebook()
    {
        return $this->social_facebook;
    }

    /**
     * @param string $social_facebook
     */
    public function setSocialFacebook(string $social_facebook): void
    {
        $this->social_facebook = $social_facebook;
    }

    /**
     * @return string
     */
    public function getSocialTwitter()
    {
        return $this->social_twitter;
    }

    /**
     * @param string $social_twitter
     */
    public function setSocialTwitter(string $social_twitter): void
    {
        $this->social_twitter = $social_twitter;
    }

    /**
     * @return string
     */
    public function getSocialGoogle()
    {
        return $this->social_google;
    }

    /**
     * @param string $social_google
     */
    public function setSocialGoogle(string $social_google): void
    {
        $this->social_google = $social_google;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $resume === null ? null : $this;
        if ($newUser !== $resume->getUser()) {
            $resume->setUser($newUser);
        }

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(?string $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getSkils()
    {
        return $this->skils;
    }

    public function setSkils(?ResumeMetadata $skils): self
    {
        $this->skils[] = $skils;

        return $this;
    }


    public function remove_skill($skill)
    {
        if ($this->skils->contains($skill))
            $this->skils->removeElement($skill);
        return $this;
    }


    public function getSocialLinks()
    {
        return $this->social_links;
    }

    public function setSocialLinks(?string $link, ?string $key): self
    {
        $this->social_links[$key] = $link;

        return $this;
    }

    public function setSocialLinkArray(array $link): self
    {
        $this->social_links = $link;

        return $this;
    }

    public function remove_socialLink($link)
    {
        if (false !== $key = array_search(strtoupper($link), $this->social_links, true)) {
            unset($this->social_links[$key]);
            $this->social_links = array_values($this->social_links);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getBookmarked()
    {
        return $this->bookmarked;
    }

    /**
     * @param mixed $bookmarked
     */
    public function setBookmarked(?string $bookmarked): void
    {
        $this->bookmarked[] = $bookmarked;
    }

    public function removeBookMarked($marked)
    {
        unset($this->bookmarked[$marked]);
        $this->bookmarked = array_values($this->bookmarked);

        return $this;
    }


    public function addBookMarket($bookMarked): self
    {
        $this->bookmarked[] = $bookMarked;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getApplied()
    {
        if ($this->applied == null) {
            return array();
        }

        return $this->applied;
    }

    /**
     * @param mixed $applied
     */
    public function setApplied(?string $applied): void
    {
        $this->applied[] = $applied;
    }

    public function removeApplied($applied)
    {
        unset($this->applied[$applied]);
        $this->applied = array_values($this->applied);

        return $this;
    }

    public function addApplied($applied): self
    {
        $this->applied[] = $applied;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHigherLevelTitlee()
    {
        return $this->higherLevelTitlee;
    }

    /**
     * @param mixed $higherLevelTitlee
     */
    public function setHigherLevelTitlee($higherLevelTitlee): void
    {
        $this->higherLevelTitlee = $higherLevelTitlee;
    }

    /**
     * @return mixed
     */
    public function getProfession()
    {
        return $this->profession;
    }

    /**
     * @param mixed $profession
     */
    public function setProfession($profession): void
    {
        $this->profession = $profession;
    }

    public function getPorcent()
    {
        if (in_array('ROLE_ADMIN', $this->getRoles())) {
            $null_fields = 0;
            foreach ($this as $key => $val) {
                if (($key == 'name') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'image') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'companyName') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'phone') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'address') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'about') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'videoIntro') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_facebook') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_twitter') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_google') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'category') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'username') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'email') && ($val == null)) {
                    $null_fields++;
                }
            }

            return round((13 - $null_fields) / 13 * 100);
        } elseif (in_array('ROLE_USER', $this->getRoles())) {
            $null_fields = 0;
            foreach ($this as $key => $val) {
                if (($key == 'name') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'image') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'phone') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'address') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'about') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'videoIntro') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_facebook') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_twitter') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'social_google') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'category') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'username') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'email') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'higherLevelTitlee') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'profession') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'status') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'experience') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'gender') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'age') && ($val == null)) {
                    $null_fields++;
                }
                if (($key == 'qualification') && ($val == null)) {
                    $null_fields++;
                }
            }

            return round((19 - $null_fields) / 19 * 100);
        }

        return 100;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJobAppiled(): Collection
    {
        return $this->jobAppiled;
    }

    public function addJobAppiled(Job $jobAppiled): self
    {
        if (!$this->jobAppiled->contains($jobAppiled)) {
            $this->jobAppiled[] = $jobAppiled;
        }

        return $this;
    }

    public function removeJobAppiled(Job $jobAppiled): self
    {
        if ($this->jobAppiled->contains($jobAppiled)) {
            $this->jobAppiled->removeElement($jobAppiled);
        }

        return $this;
    }

    public function getRoles()
    {
        return parent::getRoles();
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->category->contains($category)) {
            $this->category->removeElement($category);
        }

        return $this;
    }

    public function getCompaniesSeen(): ?int
    {
        return $this->companiesSeen;
    }

    public function setCompaniesSeen(?int $companiesSeen): self
    {
        $this->companiesSeen = $companiesSeen;

        return $this;
    }

    public function getPackageJobs()
    {
        return $this->packageJobs;
    }

    public function setPackageJobs($packageJobs): self
    {
        $this->packageJobs = $packageJobs;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackageServices()
    {
        return $this->packageServices;
    }

    /**
     * @param mixed $packageServices
     */
    public function setPackageServices($packageServices): void
    {
        $this->packageServices = $packageServices;
    }

    /**
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function isVerificated()
    {
        return $this->verificated;
    }

    /**
     * @param mixed $verificated
     */
    public function setVerificated($verificated): void
    {
        $this->verificated = $verificated;
    }

    /**
     * @return mixed
     */
    public function getVerificated()
    {
        return $this->verificated;
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
            $anouncement->setUser($this);
        }

        return $this;
    }

    public function removeAnouncement(Anouncement $anouncement): self
    {
        if ($this->anouncements->contains($anouncement)) {
            $this->anouncements->removeElement($anouncement);
            // set the owning side to null (unless already changed)
            if ($anouncement->getUser() === $this) {
                $anouncement->setUser(null);
            }
        }

        return $this;
    }

    public function getDateOfPurchase()
    {
        return $this->date_of_purchase;
    }

    public function setDateOfPurchase($date_of_purchase): self
    {
        $this->date_of_purchase = $date_of_purchase;

        return $this;
    }

    public function getNumPosts(): ?int
    {
        return $this->num_posts;
    }

    public function setNumPosts(?int $num_posts): self
    {
        $this->num_posts = $num_posts;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateOfPurchaseService()
    {
        return $this->date_of_purchase_service;
    }

    /**
     * @param mixed $date_of_purchase_service
     */
    public function setDateOfPurchaseService($date_of_purchase_service): void
    {
        $this->date_of_purchase_service = $date_of_purchase_service;
    }

    /**
     * @return mixed
     */
    public function getNumPostsServices()
    {
        return $this->num_posts_services;
    }

    /**
     * @param mixed $num_posts_services
     */
    public function setNumPostsServices($num_posts_services): void
    {
        $this->num_posts_services = $num_posts_services;
    }

    public function getServicesRequest(): ?array
    {
        return $this->servicesRequest;
    }

    public function setServicesRequest(?array $servicesRequest): self
    {
        $this->servicesRequest = $servicesRequest;

        return $this;
    }

    public function addServiceRequest($service)
    {
        if ($this->servicesRequest == null) {
            $this->servicesRequest = array();
        }
        if (!in_array($service, $this->servicesRequest)) {
            $this->servicesRequest[] = $service;
        }
    }

    public function removeServiceRequest($serice)
    {
        if (in_array($serice, $this->servicesRequest)) {
            unset($this->servicesRequest[$serice]);
        }
    }

    public function addCandidate($candidate)
    {
        if ($this->candidates == null) {
            $this->candidates = array();
        }
        if (!in_array($candidate, $this->candidates)) {
            $this->candidates[] = $candidate;
        }
    }

    public function removeCandidate($candidate)
    {
        if (in_array($candidate, $this->candidates)) {
            unset($this->candidates[$candidate]);
        }
    }

    public function getCandidates(): ?array
    {
        if ($this->candidates == null) {
            $this->candidates = array();
        }

        return $this->candidates;
    }

    public function setCandidates(?array $candidates): self
    {
        $this->candidates = $candidates;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications): void
    {
        $this->notifications = $notifications;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $company ? null : $this;
        if ($company->getUser() !== $newUser) {
            $company->setUser($newUser);
        }

        return $this;
    }

    public function addPackageJob(PaymentForJobs $packageJob): self
    {
        if (!$this->packageJobs->contains($packageJob)) {
            $this->packageJobs[] = $packageJob;
        }

        return $this;
    }

    public function removePackageJob(PaymentForJobs $packageJob): self
    {
        if ($this->packageJobs->contains($packageJob)) {
            $this->packageJobs->removeElement($packageJob);
        }

        return $this;
    }

    public function addPackageService(PaymentForServices $packageService): self
    {
        if (!$this->packageServices->contains($packageService)) {
            $this->packageServices[] = $packageService;
        }

        return $this;
    }

    public function removePackageService(PaymentForServices $packageService): self
    {
        if ($this->packageServices->contains($packageService)) {
            $this->packageServices->removeElement($packageService);
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForJobsMetadata[]
     */
    public function getPaymentForJobsMetadata(): Collection
    {
        return $this->paymentForJobsMetadata;
    }

    public function addPaymentForJobsMetadata(PaymentForJobsMetadata $paymentForJobsMetadata): self
    {
        if (!$this->paymentForJobsMetadata->contains($paymentForJobsMetadata)) {
            $this->paymentForJobsMetadata[] = $paymentForJobsMetadata;
            $paymentForJobsMetadata->setUser($this);
        }

        return $this;
    }

    public function removePaymentForJobsMetadata(PaymentForJobsMetadata $paymentForJobsMetadata): self
    {
        if ($this->paymentForJobsMetadata->contains($paymentForJobsMetadata)) {
            $this->paymentForJobsMetadata->removeElement($paymentForJobsMetadata);
            // set the owning side to null (unless already changed)
            if ($paymentForJobsMetadata->getUser() === $this) {
                $paymentForJobsMetadata->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentForServicesMetadata[]
     */
    public function getPaymentForServicesMetadata(): Collection
    {
        return $this->paymentForServicesMetadata;
    }

    public function addPaymentForServicesMetadata(PaymentForServicesMetadata $paymentForServicesMetadata): self
    {
        if (!$this->paymentForServicesMetadata->contains($paymentForServicesMetadata)) {
            $this->paymentForServicesMetadata[] = $paymentForServicesMetadata;
            $paymentForServicesMetadata->setUser($this);
        }

        return $this;
    }

    public function removePaymentForServicesMetadata(PaymentForServicesMetadata $paymentForServicesMetadata): self
    {
        if ($this->paymentForServicesMetadata->contains($paymentForServicesMetadata)) {
            $this->paymentForServicesMetadata->removeElement($paymentForServicesMetadata);
            // set the owning side to null (unless already changed)
            if ($paymentForServicesMetadata->getUser() === $this) {
                $paymentForServicesMetadata->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserJobMeta[]
     */
    public function getUserJobMetadata(): Collection
    {
        return $this->userJobMetadata;
    }

    public function addUserJobMetadata(UserJobMeta $userJobMetadata): self
    {
        if (!$this->userJobMetadata->contains($userJobMetadata)) {
            $this->userJobMetadata[] = $userJobMetadata;
            $userJobMetadata->setUser($this);
        }

        return $this;
    }

    public function removeUserJobMetadata(UserJobMeta $userJobMetadata): self
    {
        if ($this->userJobMetadata->contains($userJobMetadata)) {
            $this->userJobMetadata->removeElement($userJobMetadata);
            // set the owning side to null (unless already changed)
            if ($userJobMetadata->getUser() === $this) {
                $userJobMetadata->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadyToApply(){
        if ($this->getResume()->getCv() && $this->getResume()->getCart())
            return true;
        return false;
    }

    /**
     * @return mixed
     */
    public function getBuyFreePackService()
    {
        return $this->buyFreePackService;
    }

    /**
     * @param mixed $buyFreePackService
     */
    public function setBuyFreePackService($buyFreePackService): void
    {
        $this->buyFreePackService = $buyFreePackService;
    }

    /**
     * @return mixed
     */
    public function getBuyFreePackJob()
    {
        return $this->buyFreePackJob;
    }

    /**
     * @param mixed $buyFreePackJob
     */
    public function setBuyFreePackJob($buyFreePackJob): void
    {
        $this->buyFreePackJob = $buyFreePackJob;
    }


}