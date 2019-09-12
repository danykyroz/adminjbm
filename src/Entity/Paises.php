<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paises
 *
 * @ORM\Table(name="paises")
 * @ORM\Entity
 */
class Paises
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo_iso", type="string", length=20, nullable=true)
     */
    private $codigoIso;

    /**
     * @var int|null
     *
     * @ORM\Column(name="indicativo", type="integer", nullable=true)
     */
    private $indicativo;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodigoIso(): ?string
    {
        return $this->codigoIso;
    }

    public function setCodigoIso(?string $codigoIso): self
    {
        $this->codigoIso = $codigoIso;

        return $this;
    }

    public function getIndicativo(): ?int
    {
        return $this->indicativo;
    }

    public function setIndicativo(?int $indicativo): self
    {
        $this->indicativo = $indicativo;

        return $this;
    }


}
