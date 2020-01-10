<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="horario_empleado")
 */
class HorarioEmpleado  
{
    

       /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

     /**
     * @var \DateTime|null
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="year", type="integer", length=11, nullable=false)
     */
    private $year;

    /**
     * @var integer|null
     *
     * @ORM\Column(name="semana", type="integer", length=11, nullable=false)
     */
    private $semana;
     /**
     * @var string|null
     *
     * @ORM\Column(name="dias", type="string", length=255, nullable=true)
     */
    private $dias;

     /**
     * @var string|null
     *
     * @ORM\Column(name="empleado_id", type="string", length=255, nullable=true)
     */
    private $empleadoId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmado;




    public function getId()
    {
        return $this->id;
    }

   
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
     public function getDias(): ?string
    {
        return $this->dias;
    }

    public function setDias($dias): self
    {
        $this->dias = $dias;

        return $this;
    }


    public function setSemana($semana): self
    {
        $this->semana = $semana;

        return $this;
    }

    public function getSemana(): ?integer
    {
        return $this->semana;
    }

  
    public function getYear()
    {
       return $this->year;
    }

    public function setYear($year): self
    {
        $this->year = $year;

        return $this;
    }

     public function getEmpleadoId()
    {
        return $this->empleadoId;
    }

    public function setEmpleadoId($empleadoId): self
    {
        $this->empleadoId = $empleadoId;

        return $this;
    }

    public function getConfirmado(): ?bool
    {
        return $this->confirmado;
    }

    public function setConfirmado(?bool $confirmado): self
    {
        $this->confirmado = $confirmado;

        return $this;
    }

  

}