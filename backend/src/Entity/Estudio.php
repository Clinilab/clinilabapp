<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\ResultadoEstudio;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EstudioRepository")
 */
class Estudio
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
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Estudio")
     * @ORM\JoinColumn(nullable=true)
     */
    private $estudio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $areaid;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $abrev;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ResultadoEstudio", mappedBy="estudio")
     */
    private $resultadoEstudios;

    public function __construct()
    {
        $this->resultadoEstudios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
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

    public function getEstudio(): ?self
    {
        return $this->estudio;
    }

    public function setEstudio(?self $estudio): self
    {
        $this->estudio = $estudio;

        return $this;
    }

    public function getAreaid()
    {
        return $this->areaid;
    }

    public function setAreaid($areaid): self
    {
        $this->areaid = $areaid;

        return $this;
    }

    public function getAbrev()
    {
        return $this->abrev;
    }

    public function setAbrev($abrev): self
    {
        $this->abrev = $abrev;

        return $this;
    }

    public function getResultadoEstudios(): Collection
    {
        return $this->resultadoEstudios;
    }

    public function addResultadoEstudio(ResultadoEstudio $resultadoEstudio): self
    {
        if (!$this->resultadoEstudios->contains($resultadoEstudio)) {
            $this->resultadoEstudios[] = $resultadoEstudio;
            $resultadoEstudio->setEstudio($this);
        }

        return $this;
    }

    public function removeResultadoEstudio(ResultadoEstudio $resultadoEstudio): self
    {
        if ($this->resultadoEstudios->removeElement($resultadoEstudio)) {
            if ($resultadoEstudio->getEstudio() === $this) {
                $resultadoEstudio->setEstudio(null);
            }
        }

        return $this;
    }
}
