<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrdenEstudioRepository")
 */
class OrdenEstudio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Orden")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orden;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Estudio")
     */
    private $estudio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EstadoEstudio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $estadoEstudio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaValidacion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $imp;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $idDetalle;

       /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $estadoEnvio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    public function getOrden(): ?Orden
    {
        return $this->orden;
    }

    public function setOrden(?Orden $orden): self
    {
        $this->orden = $orden;

        return $this;
    }

    public function getEstudio(): ?Estudio
    {
        return $this->estudio;
    }

    public function setEstudio(?Estudio $estudio): self
    {
        $this->estudio = $estudio;

        return $this;
    }

    public function getEstadoEstudio(): ?EstadoEstudio
    {
        return $this->estadoEstudio;
    }

    public function setEstadoEstudio(?EstadoEstudio $estadoEstudio): self
    {
        $this->estadoEstudio = $estadoEstudio;

        return $this;
    }

    public function getFechaValidacion(): ?\DateTimeInterface
    {
        return $this->fechaValidacion;
    }

    public function setFechaValidacion(?\DateTimeInterface $fechaValidacion): self
    {
        $this->fechaValidacion = $fechaValidacion;

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

    public function getImp(): ?int
    {
        return $this->imp;
    }

    public function setImp(int $imp): self
    {
        $this->imp = $imp;

        return $this;
    }
 

    public function getIdDetalle(): ?int
    {
        return $this->idDetalle;
    }

    public function setIdDetalle(?int $idDetalle): self
    {
        $this->idDetalle = $idDetalle;

        return $this;
    }

    /**
     * Get the value of estadoEnvio
     */
    public function getEstadoEnvio()
    {
        return $this->estadoEnvio;
    }

    /**
     * Set the value of estadoEnvio
     */
    public function setEstadoEnvio($estadoEnvio): self
    {
        $this->estadoEnvio = $estadoEnvio;

        return $this;
    }
}
