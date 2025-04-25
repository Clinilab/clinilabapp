<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AreaRepository::class)
 */
class Area
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $area_cod;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $area_nombre;


    public function getId(): ?int
    {
        return $this->id;
    }
   


    /**
     * Get the value of area_cod
     */
    public function getAreaCod()
    {
        return $this->area_cod;
    }

    /**
     * Set the value of area_cod
     */
    public function setAreaCod($area_cod): self
    {
        $this->area_cod = $area_cod;

        return $this;
    }

    /**
     * Get the value of area_nombre
     */
    public function getAreaNombre()
    {
        return $this->area_nombre;
    }

    /**
     * Set the value of area_nombre
     */
    public function setAreaNombre($area_nombre): self
    {
        $this->area_nombre = $area_nombre;

        return $this;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }
}
