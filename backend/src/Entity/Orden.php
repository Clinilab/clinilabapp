<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrdenRepository")
 */
class Orden
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Eapb")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eapb;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Paciente")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paciente;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Medico")
     * @ORM\JoinColumn(nullable=false)
     */
    private $medico;

    /**
     * @ORM\Column(type="integer")
     */
    private $emb;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numExterno;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cama;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $diagnostico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $activo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Servicio")
     */
    private $servicio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaFin;
    
   /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $imporden;


     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $id_orden_externa;

      /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $id_con_maquina;


     /**
     * @ORM\Column(type="datetime", nullable=true)
     */
     private $fecha_imp;



    public function getId(): ?int
    {
        return $this->id;
    }



    public function getEmb(): ?int
    {
        return $this->emb;
    }

    public function setEmb(int $emb): self
    {
        $this->emb = $emb;

        return $this;
    }

    public function getNumExterno(): ?int
    {
        return $this->numExterno;
    }

    public function setNumExterno(int $numExterno): self
    {
        $this->numExterno = $numExterno;

        return $this;
    }


    public function getCama(): ?string
    {
        return $this->cama;
    }

    public function setCama(string $cama): self
    {
        $this->cama = $cama;

        return $this;
    }

    public function getDiagnostico(): ?string
    {
        return $this->diagnostico;
    }

    public function setDiagnostico(string $diagnostico): self
    {
        $this->diagnostico = $diagnostico;

        return $this;
    }

    public function getNotas(): ?string
    {
        return $this->notas;
    }

    public function setNotas(?string $notas): self
    {
        $this->notas = $notas;

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

    public function getEapb(): ?Eapb
    {
        return $this->eapb;
    }

    public function setEapb(?Eapb $eapb): self
    {
        $this->eapb = $eapb;

        return $this;
    }

    public function getPaciente(): ?Paciente
    {
        return $this->paciente;
    }

    public function setPaciente(?Paciente $paciente): self
    {
        $this->paciente = $paciente;

        return $this;
    }

    public function getMedico(): ?Medico
    {
        return $this->medico;
    }

    public function setMedico(?Medico $medico): self
    {
        $this->medico = $medico;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getServicio(): ?Servicio
    {
        return $this->servicio;
    }

    public function setServicio(?Servicio $servicio): self
    {
        $this->servicio = $servicio;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getFechaFin(): ?\DateTimeInterface
    {
        return $this->fechaFin;
    }

    public function setFechaFin(?\DateTimeInterface $fechaFin): self
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }


    /**
     * Get the value of imporden
     */
    public function getImporden()
    {
        return $this->imporden;
    }

    /**
     * Set the value of imporden
     */
    public function setImporden($imporden): self
    {
        $this->imporden = $imporden;

        return $this;
    }

    /**
     * Set the value of id_orden_externa
     */
    public function setIdOrdenExterna($id_orden_externa): self
    {
        $this->id_orden_externa = $id_orden_externa;

        return $this;
    }

    /**
     * Get the value of id_orden_externa
     */
    public function getIdOrdenExterna()
    {
        return $this->id_orden_externa;
    }



    

    /**
     * Get the value of id_con_maquina
     */
    public function getIdConMaquina() : ?string
    {
        return $this->id_con_maquina;
    }

    /**
     * Set the value of id_con_maquina
     */
    public function setIdConMaquina($id_con_maquina): self
    {
        $this->id_con_maquina = $id_con_maquina;

        return $this;
    }

     /**
      * Get the value of fecha_imp
      */
     public function getFechaImp()
     {
          return $this->fecha_imp;
     }

     /**
      * Set the value of fecha_imp
      */
     public function setFechaImp(?\DateTimeInterface $fecha_imp): self
     {
        
          $this->fecha_imp = $fecha_imp;

          return $this;
     }
}
