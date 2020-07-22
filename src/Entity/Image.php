<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable
 * @ORM\Table
 */
class Image
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
    private $image;
	
	/**
	 * @Assert\Image(
	 *     allowLandscape = true,
	 *     allowPortrait = true
	 * )
	 * @Vich\UploadableField(mapping="image_store", fileNameProperty="image")
	 * @var File
	 */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updateAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Anouncement", inversedBy="images")
     */
    private $ManyToOne;
    
    public function __construct()
    {
    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile():?File
    {
        return $this->imageFile;
    }

    public function setImageFile(File $imageFile): self
    {
	    $this->imageFile = $imageFile;
	    if ($imageFile instanceof UploadedFile) {
		    $this->setUpdateAt(new \DateTime());
	    }
	    return $this;
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

    public function getManyToOne(): ?Anouncement
    {
        return $this->ManyToOne;
    }

    public function setManyToOne(?Anouncement $ManyToOne): self
    {
        $this->ManyToOne = $ManyToOne;

        return $this;
    }
    
}
