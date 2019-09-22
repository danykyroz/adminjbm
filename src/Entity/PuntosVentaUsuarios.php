<?php

namespace App\Entity;

use App\Entity\PuntosVenta;
use App\Repository\PuntosVentaUsuariosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * PuntosVentaUsuarios
 * @ORM\Entity(repositoryClass="App\Repository\PuntosVentaUsuariosRepository")
*/
class PuntosVentaUsuarios
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
     * @ORM\Column(name="usuario_id", type="integer", nullable=true)
     */
    private $usuarioId;

     /**
     * @var int|null
     *
     * @ORM\Column(name="puntoventa_id", type="integer", nullable=true)
     */
    private $puntoventaId;

    /**
     * @var \PuntoVenta
     *
     * @ORM\ManyToOne(targetEntity="PuntosVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="puntoventa_id", referencedColumnName="id")
     * })
     */
    private $puntoventa;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPuntoventaId(): ?int
    {
        return $this->puntoventaId;
    }

    public function setPuntoventaId(?int $puntoventaId): self
    {
        $this->puntoventaId = $puntoventaId;

        return $this;
    }

    public function getPuntoVenta(): ?PuntosVenta
    {
        return $this->puntoventa;
    }

    public function setPuntoVenta(?PuntosVenta $puntoventa): self
    {
        $this->puntoventa = $puntoventa;

        return $this;
    }


}
