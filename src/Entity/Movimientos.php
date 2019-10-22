<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Wallet;
use App\Entity\FosUser;

/**
 * Movimientos
 *
 * @ORM\Table(name="movimientos", indexes={@ORM\Index(name="fos_user_id", columns={"fos_user_id"}), @ORM\Index(name="cliente_id", columns={"wallet_id"}), @ORM\Index(name="tipo_movimoento_id", columns={"tipo_movimiento_id"})})
 * @ORM\Entity
 */
class Movimientos
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
     * @ORM\Column(name="updated_at", type="date", nullable=true)
     */
    private $updatedAt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="valor", type="float", precision=19, scale=2, nullable=true)
     */
    private $valor;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sincronizado", type="integer", nullable=true)
     */
    private $sincronizado;

    /**
     * @var \wallet
     *
     * @ORM\ManyToOne(targetEntity="Wallet")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wallet_id", referencedColumnName="id")
     * })
     */
    private $wallet;

    /**
     * @var \FosUser
     *
     * @ORM\ManyToOne(targetEntity="FosUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fos_user_id", referencedColumnName="id")
     * })
     */
    private $fosUser;

    /**
     * @var \TipoMovimientos
     *
     * @ORM\ManyToOne(targetEntity="TipoMovimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_movimiento_id", referencedColumnName="id")
     * })
     */
    private $tipoMovimiento;


    /**
     * @var string|null
     *
     * @ORM\Column(name="notas", type="text", length=200, nullable=true)
     */
    private $notas;


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

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(?float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getSincronizado(): ?int
    {
        return $this->sincronizado;
    }

    public function setSincronizado(?int $sincronizado): self
    {
        $this->sincronizado = $sincronizado;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getFosUser(): ?FosUser
    {
        return $this->fosUser;
    }

    public function setFosUser(?FosUser $fosUser): self
    {
        $this->fosUser = $fosUser;

        return $this;
    }

    public function getTipoMovimiento(): ?TipoMovimientos
    {
        return $this->tipoMovimiento;
    }

    public function setTipoMovimiento(?TipoMovimientos $tipoMovimiento): self
    {
        $this->tipoMovimiento = $tipoMovimiento;

        return $this;
    }

    public function getNotas(): ?string
    {
        return $this->notas;
    }

    public function setNotas(?string $notas): self
    {
        $this->notas = $notas;

        return $this;
    }


}
