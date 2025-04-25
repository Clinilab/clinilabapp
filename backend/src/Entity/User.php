<?php
// src/Entity/User.php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
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
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $nombres;

     /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $apellidos;

     /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $telefono;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TipoIdentificacion")
     */
    private $tipoIdentificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $identificacion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genero;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $firma;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(?string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getIdentificacion(): ?string
    {
        return $this->identificacion;
    }

    public function setIdentificacion(string $identificacion): self
    {
        $this->identificacion = $identificacion;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(string $genero): self
    {
        $this->genero = $genero;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->firma;
    }

    public function setFirma(?string $firma): self
    {
        $this->firma = $firma;

        return $this;
    }

    public function getTipoIdentificacion(): ?TipoIdentificacion
    {
        return $this->tipoIdentificacion;
    }

    public function setTipoIdentificacion(?TipoIdentificacion $tipoIdentificacion): self
    {
        $this->tipoIdentificacion = $tipoIdentificacion;

        return $this;
    }

    
}