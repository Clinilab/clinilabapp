<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConsecutivoRepository")
 */
class Consecutivo
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
    private $prefijo;

    /**
     * @ORM\Column(type="bigint")
     */
    private $consecutivo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\Column(type="string", length=255)
     */
     private $nombre;

    /**
     * @ORM\Column(type="date")
     */

    public $fechaactual;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrefijo(): ?string
    {
        return $this->prefijo;
    }

    public function setPrefijo(string $prefijo): self
    {
        $this->prefijo = $prefijo;

        return $this;
    }

    public function getConsecutivo(): ?string
    {
        return $this->consecutivo;
    }

    public function setConsecutivo(string $consecutivo): self
    {
        $this->consecutivo = $consecutivo;

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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

  

    /**
     * Get the value of fechaactual
     */
    public function getFechaactual()
    {
        return $this->fechaactual;
    }

    /**
     * Set the value of fechaactual
     */
    public function setFechaactual($fechaactual): self
    {
        $this->fechaactual = $fechaactual;

        return $this;
    }
}
