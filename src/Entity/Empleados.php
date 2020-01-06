<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="empleados")
 */
class Empleados  
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
     * @var string|null
     *
     * @ORM\Column(name="nombres", type="string", length=255, nullable=false)
     */
    private $nombres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cargo", type="string", length=255, nullable=false)
     */
    private $cargo;
     /**
     * @var string|null
     *
     * @ORM\Column(name="documento", type="string", length=20, nullable=true)
     */
    private $documento;

     /**
     * @var string|null
     *
     * @ORM\Column(name="dias_descanso", type="string", length=255, nullable=true)
     */
    private $diasDescanso;


    /**
     * @var string|null
     *
     * @ORM\Column(name="cliente_id", type="string", length=255, nullable=true)
     */
    private $clienteId;




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
     public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }


    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getCargo(): ?string
    {
        return $this->cargo;
    }

    public function setCargo(string $cargo): self
    {
        $this->cargo = $cargo;

        return $this;
    }


    public function getDiasDescanso()
    {
        return json_decode($this->diasDescanso);
    }

    public function setDiasDescanso($diasDescanso): self
    {
        $this->diasDescanso = $diasDescanso;

        return $this;
    }

     public function getClienteId()
    {
        return $this->clienteId;
    }

    public function setClienteId($clienteId): self
    {
        $this->clienteId = $clienteId;

        return $this;
    }

  

}