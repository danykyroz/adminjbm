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
     * @ORM\Column(name="documento", type="string", length=11, nullable=false)
     */
    private $documento = '';

    /**
     * @var string
     *
     * @ORM\Column(name="nombres", type="string", length=50, nullable=false)
     */
    private $nombres = '';

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=50, nullable=false)
     */
    private $apellidos = '';

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email = '';

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=11, nullable=false, options={"default"="MX"})
     */
    private $pais = 'MX';

   
    /**
     * Many-to-one relationship between documents and course
     *
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="App\Entity\Delegacion")
     * @ORM\JoinColumn(name="delegacion_id", referencedColumnName="id")
    */
    private $delegacion;

    /**
     * @var int
     *
     * @ORM\Column(name="indicativo", type="integer", nullable=false, options={"default"="52"})
     */
    private $indicativo = '52';

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=false)
     */
    private $celular = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="placa", type="string", length=10, nullable=true)
     */
    private $placa = '';

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
     * @var bool|null
     *
     * @ORM\Column(name="terminos", type="boolean", nullable=true, options={"default"="1"})
     */
    private $terminos = '1';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="politicas", type="boolean", nullable=true, options={"default"="1"})
     */
    private $politicas = '1';

    /**
     * @var int|null
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true, options={"default"="1","comment"="1=Normal,2=Flotilla"})
     */
    private $tipo = '1';

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

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

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

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

     public function getDelegacion(): ?Delegacion
    {
        return $this->delegacion;
    }

    public function setDelegacion(?Delegacion $delegacion): self
    {
        $this->delegacion = $delegacion;

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

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getPlaca(): ?string
    {
        return $this->placa;
    }

    public function setPlaca(?string $placa): self
    {
        $this->placa = $placa;

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

    public function getTerminos(): ?bool
    {
        return $this->terminos;
    }

    public function setTerminos(?bool $terminos): self
    {
        $this->terminos = $terminos;

        return $this;
    }

    public function getPoliticas(): ?bool
    {
        return $this->politicas;
    }

    public function setPoliticas(?bool $politicas): self
    {
        $this->politicas = $politicas;

        return $this;
    }

    public function getTipo(): ?int
    {
        return $this->tipo;
    }

    public function setTipo(?int $tipo): self
    {
        $this->tipo = $tipo;

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
