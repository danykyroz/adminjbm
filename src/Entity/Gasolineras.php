<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Delegacion as Delegacion;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Gasolineras
 *
 * @ORM\Table(name="gasolineras")
 * @ORM\Entity(repositoryClass="App\Repository\GasolinerasRepository")
 */
class Gasolineras
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
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=11, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_encargado", type="string", length=50, nullable=true)
     */
    private $nombreEncargado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefono_encargado", type="string", length=50, nullable=true)
     */
    private $telefonoEncargado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="delegacion_id", type="integer", nullable=true)
     */
    private $delegacionId;

    /**
     * Many-to-one relationship between documents and course
     *
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="App\Entity\Delegacion")
     * @ORM\JoinColumn(name="delegacion_id", referencedColumnName="id")
    */
    private $delegacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_postal", type="string", length=11, nullable=true)
     */
    private $codigoPostal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitud", type="float", precision=10, scale=0, nullable=true)
     */
    private $latitud;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitud", type="float", precision=10, scale=0, nullable=true)
     */
    private $longitud;

   public function getId(): ?int
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getNombreEncargado(): ?string
    {
        return $this->nombreEncargado;
    }

    public function setNombreEncargado(?string $nombreEncargado): self
    {
        $this->nombreEncargado = $nombreEncargado;

        return $this;
    }

    public function getTelefonoEncargado(): ?string
    {
        return $this->telefonoEncargado;
    }

    public function setTelefonoEncargado(?string $telefonoEncargado): self
    {
        $this->telefonoEncargado = $telefonoEncargado;

        return $this;
    }

    public function getDelegacionId(): ?int
    {
        return $this->delegacionId;
    }

    public function setDelegacionId($delegacionId): self
    {
        $this->delegacionId = $delegacionId;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getCodigoPostal(): ?string
    {
        return $this->codigoPostal;
    }

    public function setCodigoPostal(?string $codigoPostal): self
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    public function getLatitud(): ?float
    {
        return $this->latitud;
    }

    public function setLatitud(?float $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?float
    {
        return $this->longitud;
    }

    public function setLongitud(?float $longitud): self
    {
        $this->longitud = $longitud;

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

    public function __toString(){
        return $this->nombre;
    }

    

}
