<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultadoEstudioRepository")
 */
class ResultadoEstudio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $nota;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $variableMaquina;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $formula;


    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $opciones = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $rangos = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $posicion;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Estudio", inversedBy="resultadoEstudios")
     */
    private $estudio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UnidadMedida")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unidadMedida;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNota(): ?string
    {
        return $this->nota;
    }

    public function setNota(string $nota): self
    {
        $this->nota = $nota;

        return $this;
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

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getVariableMaquina(): ?string
    {
        return $this->variableMaquina;
    }

    public function setVariableMaquina(?string $variableMaquina): self
    {
        $this->variableMaquina = $variableMaquina;

        return $this;
    }

    public function getFormula(): ?string
    {
        return $this->formula;
    }

    public function setFormula(?string $formula): self
    {
        $this->formula = $formula;

        return $this;
    }

    public function getOpciones(): ?array
    {
        return $this->opciones;
    }

    public function setOpciones(?array $opciones): self
    {
        $this->opciones = $opciones;

        return $this;
    }

    public function getRangos(): ?array
    {
        return $this->rangos;
    }

    public function setRangos(?array $rangos): self
    {
        $this->rangos = $rangos;

        return $this;
    }

    public function getPosicion(): ?int
    {
        return $this->posicion;
    }

    public function setPosicion(int $posicion): self
    {
        $this->posicion = $posicion;

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

    public function getEstudio(): ?Estudio
    {
        return $this->estudio;
    }

    public function setEstudio(?Estudio $estudio): self
    {
        $this->estudio = $estudio;

        return $this;
    }

    public function getUnidadMedida(): ?UnidadMedida
    {
        return $this->unidadMedida;
    }

    public function setUnidadMedida(?UnidadMedida $unidadMedida): self
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }
        
}
