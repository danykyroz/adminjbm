<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoMovimientos
 *
 * @ORM\Table(name="tipo_movimientos")
 * @ORM\Entity
 */
class TipoMovimientos
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
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="operacion", type="string", length=5, nullable=true)
     */
    private $operacion;

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

    public function getOperacion(): ?string
    {
        return $this->operacion;
    }

    public function setOperacion(?string $operacion): self
    {
        $this->operacion = $operacion;

        return $this;
    }


}
