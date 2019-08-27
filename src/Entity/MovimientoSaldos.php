<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoSaldos
 *
 * @ORM\Table(name="movimiento_saldos", indexes={@ORM\Index(name="movimiento_id", columns={"movimiento_id"})})
 * @ORM\Entity
 */
class MovimientoSaldos
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
     * @var float|null
     *
     * @ORM\Column(name="saldo_anterior", type="float", precision=19, scale=2, nullable=true)
     */
    private $saldoAnterior;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor", type="float", precision=19, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @var float|null
     *
     * @ORM\Column(name="nuevo_saldo", type="float", precision=19, scale=2, nullable=true)
     */
    private $nuevoSaldo;

    /**
     * @var \Movimientos
     *
     * @ORM\ManyToOne(targetEntity="Movimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="movimiento_id", referencedColumnName="id")
     * })
     */
    private $movimiento;

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

    public function getSaldoAnterior(): ?float
    {
        return $this->saldoAnterior;
    }

    public function setSaldoAnterior(?float $saldoAnterior): self
    {
        $this->saldoAnterior = $saldoAnterior;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(?float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getNuevoSaldo(): ?float
    {
        return $this->nuevoSaldo;
    }

    public function setNuevoSaldo(?float $nuevoSaldo): self
    {
        $this->nuevoSaldo = $nuevoSaldo;

        return $this;
    }

    public function getMovimiento(): ?Movimientos
    {
        return $this->movimiento;
    }

    public function setMovimiento(?Movimientos $movimiento): self
    {
        $this->movimiento = $movimiento;

        return $this;
    }


}
