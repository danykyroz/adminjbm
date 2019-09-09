<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuponesDescuento
 *
 * @ORM\Table(name="cupones_descuento")
 * @ORM\Entity
 */
class CuponesDescuento
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
     * @ORM\Column(name="fecha_inicial", type="datetime", nullable=true)
     */
    private $fechaInicial;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tipo", type="integer", nullable=true, options={"comment"="1=Porcentaje,2=Valor Fijo"})
     */
    private $tipo;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor", type="float", precision=11, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=20, nullable=true)
     */
    private $codigo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=true, options={"comment"="-1=Infinito"})
     */
    private $cantidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mensajeok", type="text", length=65535, nullable=true)
     */
    private $mensajeok;

    /**
     * @var int|null
     *
     * @ORM\Column(name="estado", type="integer", nullable=true, options={"comment"="1=Activo,2=Inactivo"})
     */
    private $estado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaInicial(): ?\DateTimeInterface
    {
        return $this->fechaInicial;
    }

    public function setFechaInicial(?\DateTimeInterface $fechaInicial): self
    {
        $this->fechaInicial = $fechaInicial;

        return $this;
    }

    public function getFechaVencimiento(): ?\DateTimeInterface
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento(?\DateTimeInterface $fechaVencimiento): self
    {
        $this->fechaVencimiento = $fechaVencimiento;

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

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(?float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(?int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getMensajeok(): ?string
    {
        return $this->mensajeok;
    }

    public function setMensajeok(?string $mensajeok): self
    {
        $this->mensajeok = $mensajeok;

        return $this;
    }

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(?int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }


}
