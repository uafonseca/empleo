<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlertRepository")
 * @ORM\Table
 */
class Alert
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es válido."
     * )
     *  @ORM\Column(type="string")
     */ 
    private $email;

    /**
     * @var array
     * Undocumented variable
     * @ORM\Column(type="array")
     */
    private $keywords;

    
    /**
     * Undocumented function
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @return self
     */
    public function setId(int $id):self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function getEmail():?string
    {
        return $this->email;
    }

    /**
     * Undocumented function
     *
     * @param string $email
     * @return self
     */
    public function setEmail(string $email):self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function getKeywords():?array
    {
        return $this->keywords;
    }

    /**
     * Undocumented function
     *
     * @param array $keywors
     * @return self
     */
    public function seKeywords(array $keywors):self
    {
        $this->keywords = $keywors;
        return $this;
    }
}
