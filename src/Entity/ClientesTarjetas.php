<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientesTarjetas
 *
 * @ORM\Table(name="clientes_tarjetas")
 * @ORM\Entity
 */
class ClientesTarjetas
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
     * @ORM\Column(name="franquicia", type="string", length=20, nullable=true)
     */
    private $franquicia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="token", type="string", length=200, nullable=true)
     */
    private $token;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero", type="string", length=50, nullable=true)
     */
    private $numero;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pasarela_id", type="integer", nullable=true)
     */
    private $pasarelaId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fecha_expiracion", type="string", length=20, nullable=true)
     */
    private $fechaExpiracion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="wallet_id", type="integer", nullable=true)
     */
    private $walletId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFranquicia(): ?string
    {
        return $this->franquicia;
    }

    public function setFranquicia(?string $franquicia): self
    {
        $this->franquicia = $franquicia;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getPasarelaId(): ?int
    {
        return $this->pasarelaId;
    }

    public function setPasarelaId(?int $pasarelaId): self
    {
        $this->pasarelaId = $pasarelaId;

        return $this;
    }

    public function getFechaExpiracion(): ?string
    {
        return $this->fechaExpiracion;
    }

    public function setFechaExpiracion(?string $fechaExpiracion): self
    {
        $this->fechaExpiracion = $fechaExpiracion;

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


}
