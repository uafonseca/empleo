<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnouncementRepository")
 * @Vich\Uploadable
 */
class Anouncement
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
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="anouncements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profession;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $experience;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Location;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addres1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addres2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $video_link;

//    /**
//     * @ORM\Column(type="string", length=255)
//     */
//    private $package;
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
	 * @Vich\UploadableField(mapping="image_store", fileNameProperty="image")
	 * @var File
	 */
	protected $imageFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="anouncement")
     */
    private $galery;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="date")
     */
    private $expired_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="anouncements")
     */
    private $User;
    

    public function __construct()
    {
        $this->galery = new ArrayCollection();
        
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

    public function getProfession(): ?Service
    {
        return $this->profession;
    }

    public function setProfession(?Service $profession): self
    {
        $this->profession = $profession;

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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

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

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(string $Location): self
    {
        $this->Location = $Location;

        return $this;
    }

    public function getAddres1(): ?string
    {
        return $this->addres1;
    }

    public function setAddres1(?string $addres1): self
    {
        $this->addres1 = $addres1;

        return $this;
    }

    public function getAddres2(): ?string
    {
        return $this->addres2;
    }

    public function setAddres2(?string $addres2): self
    {
        $this->addres2 = $addres2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

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

    public function getVideoLink(): ?string
    {
        return $this->video_link;
    }

    public function setVideoLink(?string $video_link): self
    {
        $this->video_link = $video_link;

        return $this;
    }

//    public function getPackage(): ?string
//    {
//        return $this->package;
//    }
//
//    public function setPackage(string $package): self
//    {
//        $this->package = $package;
//
//        return $this;
//    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getGalery(): Collection
    {
        return $this->galery;
    }

    public function addGalery(Image $galery): self
    {
        if (!$this->galery->contains($galery)) {
            $this->galery[] = $galery;
            $galery->setAnouncement($this);
        }

        return $this;
    }

    public function removeGalery(Image $galery): self
    {
        if ($this->galery->contains($galery)) {
            $this->galery->removeElement($galery);
            // set the owning side to null (unless already changed)
            if ($galery->getAnouncement() === $this) {
                $galery->setAnouncement(null);
            }
        }

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

    public function getExpiredDate(): ?\DateTimeInterface
    {
        return $this->expired_date;
    }

    public function setExpiredDate(\DateTimeInterface $expired_date): self
    {
        $this->expired_date = $expired_date;

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

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
	
	/**
	 * @return File
	 */
	public function getImageFile()
	{
		return $this->imageFile;
	}
	
	/**
	 * @param File $imageFile
	 */
	public function setImageFile(File $imageFile): void
	{
		$this->imageFile = $imageFile;
	}
    
    
}
