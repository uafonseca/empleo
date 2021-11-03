<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity
 * @ORM\Table
 */
class Consulta
{

    const TYPE_LEGAL = 'Legal';
    const TYPE_ADMINISTRATIVO = 'Administrativo';
    const TYPE_CONTABLE = 'Contable';
    const TYPE_CODIGO_TRABAJO = 'Código de trabajo';
    const TYPE_LOSEP = 'Losep';
    const TYPE_RIESGOS_LABORALES = 'Riesgos laborales';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $texto;

    /**
     * @ORM\Column(type="string")
     */
    private $ciudad;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="consultas")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=RespuestaConsulta::class, mappedBy="consulta")
     */
    private $respuestas;

    public function __construct()
    {
        $this->respuestas = new ArrayCollection();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  \DateTime  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of texto
     */
    public function getTexto()
    {
        return $this->texto;
    }

    /**
     * Set the value of texto
     *
     * @return  self
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;

        return $this;
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
    public function getCiudad()
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad){
        $this->ciudad = $ciudad;
    }

    /**
     * @return Collection|RespuestaConsulta[]
     */
    public function getRespuestas(): Collection
    {
        return $this->respuestas;
    }

    public function addRespuesta(RespuestaConsulta $respuesta): self
    {
        if (!$this->respuestas->contains($respuesta)) {
            $this->respuestas[] = $respuesta;
            $respuesta->setConsulta($this);
        }

        return $this;
    }

    public function removeRespuesta(RespuestaConsulta $respuesta): self
    {
        if ($this->respuestas->removeElement($respuesta)) {
            // set the owning side to null (unless already changed)
            if ($respuesta->getConsulta() === $this) {
                $respuesta->setConsulta(null);
            }
        }

        return $this;
    }
}
