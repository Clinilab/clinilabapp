<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ValorResultadoRepository")
 */
class ValorResultado
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
    private $valor;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ResultadoEstudio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $resultadoEstudio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\OrdenEstudio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ordenEstudio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaModificacion;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = $valor;

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

    public function getResultadoEstudio(): ?ResultadoEstudio
    {
        return $this->resultadoEstudio;
    }

    public function setResultadoEstudio(?ResultadoEstudio $resultadoEstudio): self
    {
        $this->resultadoEstudio = $resultadoEstudio;

        return $this;
    }

    public function getOrdenEstudio(): ?OrdenEstudio
    {
        return $this->ordenEstudio;
    }

    public function setOrdenEstudio(?OrdenEstudio $ordenEstudio): self
    {
        $this->ordenEstudio = $ordenEstudio;

        return $this;
    }

    public function getFechaModificacion(): ?\DateTimeInterface
    {
        return $this->fechaModificacion;
    }

    public function setFechaModificacion(?\DateTimeInterface $fechaModificacion): self
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

}
