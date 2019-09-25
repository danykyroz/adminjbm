<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Gasolineras;

/**
 * GasolineraUsuarios
 *
 * @ORM\Table(name="gasolinera_usuarios")
 * @ORM\Entity
 */
class GasolineraUsuarios
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
     * @ORM\Column(name="gasolinera_id", type="integer", nullable=true)
     */
    private $gasolineraId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=true)
     */
    private $usuarioId;

    /**
     * @var \Gasolineras
     *
     * @ORM\ManyToOne(targetEntity="Gasolineras")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gasolinera_id", referencedColumnName="id")
     * })
     */
    private $gasolinera;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGasolineraId(): ?int
    {
        return $this->gasolineraId;
    }

    public function setGasolineraId(?int $gasolineraId): self
    {
        $this->gasolineraId = $gasolineraId;

        return $this;
    }

    public function getUsuarioId(): ?int
    {
        return $this->usuarioId;
    }

    public function setUsuarioId(?int $usuarioId): self
    {
        $this->usuarioId = $usuarioId;

        return $this;
    }

    public function getGasolinera(): ?Gasolineras
    {
        return $this->gasolinera;
    }

    public function setGasolinera(?Gasolineras $gasolinera): self
    {
        $this->gasolinera = $gasolinera;

        return $this;
    }


}
