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
     * @var int|null
     *
     * @ORM\Column(name="nombre", type="integer", nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo_iso", type="integer", nullable=true)
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

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(?int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCodigoIso(): ?int
    {
        return $this->codigoIso;
    }

    public function setCodigoIso(?int $codigoIso): self
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
