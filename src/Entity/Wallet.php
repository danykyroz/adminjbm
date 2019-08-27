<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Wallet
 *
 * @ORM\Table(name="wallet")
 * @ORM\Entity
 */
class Wallet
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
     * @var int|null
     *
     * @ORM\Column(name="cliente_id", type="integer", nullable=true)
     */
    private $clienteId;

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
     * @var string|null
     *
     * @ORM\Column(name="qr", type="string", length=600, nullable=true)
     */
    private $qr;

    /**
     * @var float|null
     *
     * @ORM\Column(name="saldo", type="float", precision=19, scale=2, nullable=true)
     */
    private $saldo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClienteId(): ?int
    {
        return $this->clienteId;
    }

    public function setClienteId(?int $clienteId): self
    {
        $this->clienteId = $clienteId;

        return $this;
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

    public function getQr(): ?string
    {
        return $this->qr;
    }

    public function setQr(?string $qr): self
    {
        $this->qr = $qr;

        return $this;
    }

    public function getSaldo(): ?float
    {
        return $this->saldo;
    }

    public function setSaldo(?float $saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }


}
