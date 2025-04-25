<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

 /**
 * @ORM\Entity(repositoryClass="App\Repository\ReportesSqlRepository")
 */

class ReportesSql
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
    private $sqlNombre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="text")
     */
    private $consulta;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaCreacion;

    // Getters and setters...

    /**
     * Get /* @ORM\Id()
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Get the value of sqlNombre
     */
    public function getSqlNombre()
    {
        return $this->sqlNombre;
    }

    /**
     * Set the value of sqlNombre
     */
    public function setSqlNombre($sqlNombre): self
    {
        $this->sqlNombre = $sqlNombre;

        return $this;
    }

    /**
     * Get the value of descripcion
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     */
    public function setDescripcion($descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get the value of consulta
     */
    public function getConsulta()
    {
        return $this->consulta;
    }

    /**
     * Set the value of consulta
     */
    public function setConsulta($consulta): self
    {
        $this->consulta = $consulta;

        return $this;
    }

 

    /**
     * Get the value of fechaCreacion
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set the value of fechaCreacion
     */
    public function setFechaCreacion($fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }
}
