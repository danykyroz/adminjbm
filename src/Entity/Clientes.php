<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Delegacion as Delegacion;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Clientes
 *
 * @ORM\Table(name="clientes")
 * @ORM\Entity
 */
class Clientes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=50, nullable=false)
     */
    private $documento = '';

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=50, nullable=false)
     */
    private $razonSocial = '';

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=11, nullable=false, options={"default"="MX"})
     */
    private $pais = 'MX';

    /**
     * @var int
     *
     * @ORM\Column(name="indicativo", type="integer", nullable=false, options={"default"="52"})
     */
    private $indicativo = '52';


    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=false)
     */
    private $celular = '';


    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=false)
     */
    private $direccion = '';

    /**
     * @var \DateTime|null
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="auxiliar_id", type="integer", nullable=false)
     */
    private $auxiliarId;

    
    /**
     * @var int|null
     *
     * @ORM\Column(name="estado", type="integer", nullable=true, options={"default"="1","comment"="1=Normal,2=Flotilla"})
     */
    private $estado= '1';

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRazonSocial(): ?string
    {
        return $this->razonSocial;
    }

    public function setRazonSocial(string $razonSocial): self
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    
    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

   
    public function getIndicativo(): ?int
    {
        return $this->indicativo;
    }

    public function setIndicativo(int $indicativo): self
    {
        $this->indicativo = $indicativo;

        return $this;
    }

     public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

     public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getAuxiliarId(): ?int
    {
        return $this->auxiliarId;
    }

    public function setAuxiliarId(int $auxiliarId): self
    {
        $this->auxiliarId = $auxiliarId;

        return $this;
    }

   
    public function getEstado()
    {
        if($this->estado==0){
            return 'suspendido';
        }else{
             return 'Activo';   
        }
    }

    public function setEstado(?int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }


}
