<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transacciones
 *
 * @ORM\Table(name="transacciones")
 * @ORM\Entity
 */
class Transacciones
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_pago", type="datetime", nullable=true)
     */
    private $fechaPago;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor", type="float", precision=19, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wallet_id", type="integer", nullable=true)
     */
    private $walletId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="gasolinera_id", type="integer", nullable=true)
     */
    private $gasolineraId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="punto_venta_id", type="integer", nullable=true)
     */
    private $puntoVentaId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="flotilla_id", type="integer", nullable=true)
     */
    private $flotillaId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tipo_transaccion", type="integer", nullable=true, options={"comment"="1=Pago,2=Recarga"})
     */
    private $tipoTransaccion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="usuario", type="integer", nullable=true)
     */
    private $usuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado", type="string", length=50, nullable=true)
     */
    private $estado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="respuesta", type="string", length=255, nullable=true)
     */
    private $respuesta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cod_respuesta", type="integer", nullable=true)
     */
    private $codRespuesta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", length=50, nullable=true)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dispositivo", type="string", length=200, nullable=true)
     */
    private $dispositivo;

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

    public function getFechaPago(): ?\DateTimeInterface
    {
        return $this->fechaPago;
    }

    public function setFechaPago(?\DateTimeInterface $fechaPago): self
    {
        $this->fechaPago = $fechaPago;

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

    public function getWalletId(): ?int
    {
        return $this->walletId;
    }

    public function setWalletId(?int $walletId): self
    {
        $this->walletId = $walletId;

        return $this;
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

    public function getPuntoVentaId(): ?int
    {
        return $this->puntoVentaId;
    }

    public function setPuntoVentaId(?int $puntoVentaId): self
    {
        $this->puntoVentaId = $puntoVentaId;

        return $this;
    }

    public function getFlotillaId(): ?int
    {
        return $this->flotillaId;
    }

    public function setFlotillaId(?int $flotillaId): self
    {
        $this->flotillaId = $flotillaId;

        return $this;
    }

    public function getTipoTransaccion(): ?int
    {
        return $this->tipoTransaccion;
    }

    public function setTipoTransaccion(?int $tipoTransaccion): self
    {
        $this->tipoTransaccion = $tipoTransaccion;

        return $this;
    }

    public function getUsuario(): ?int
    {
        return $this->usuario;
    }

    public function setUsuario(?int $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getRespuesta(): ?string
    {
        return $this->respuesta;
    }

    public function setRespuesta(?string $respuesta): self
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    public function getCodRespuesta(): ?int
    {
        return $this->codRespuesta;
    }

    public function setCodRespuesta(?int $codRespuesta): self
    {
        $this->codRespuesta = $codRespuesta;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getDispositivo(): ?string
    {
        return $this->dispositivo;
    }

    public function setDispositivo(?string $dispositivo): self
    {
        $this->dispositivo = $dispositivo;

        return $this;
    }


}
