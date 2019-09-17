<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * FlotillasClientes
 *
 * @ORM\Table(name="flotillas_clientes")
 * @ORM\Entity
 */
class FlotillasClientes
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
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cliente_id", type="integer", nullable=true)
     */
    private $clienteId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="flotilla_id", type="integer", nullable=true)
     */
    private $flotillaId;

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

    public function getClienteId(): ?int
    {
        return $this->clienteId;
    }

    public function setClienteId(?int $clienteId): self
    {
        $this->clienteId = $clienteId;

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


}
