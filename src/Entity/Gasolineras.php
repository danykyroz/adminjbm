<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gasolineras
 *
 * @ORM\Table(name="gasolineras")
 * @ORM\Entity
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
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nombre", type="integer", nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ciudad_id", type="integer", nullable=true)
     */
    private $ciudadId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="latitud", type="integer", nullable=true)
     */
    private $latitud;

    /**
     * @var int|null
     *
     * @ORM\Column(name="longitud", type="integer", nullable=true)
     */
    private $longitud;

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

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(?int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCiudadId(): ?int
    {
        return $this->ciudadId;
    }

    public function setCiudadId(?int $ciudadId): self
    {
        $this->ciudadId = $ciudadId;

        return $this;
    }

    public function getLatitud(): ?int
    {
        return $this->latitud;
    }

    public function setLatitud(?int $latitud): self
    {
        $this->latitud = $latitud;

        return $this;
    }

    public function getLongitud(): ?int
    {
        return $this->longitud;
    }

    public function setLongitud(?int $longitud): self
    {
        $this->longitud = $longitud;

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


}
