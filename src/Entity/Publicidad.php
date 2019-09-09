<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publicidad
 *
 * @ORM\Table(name="publicidad")
 * @ORM\Entity
 */
class Publicidad
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
     * @ORM\Column(name="upated_at", type="datetime", nullable=true)
     */
    private $upatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="imagen", type="integer", nullable=true)
     */
    private $imagen;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

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

    public function getUpatedAt(): ?\DateTimeInterface
    {
        return $this->upatedAt;
    }

    public function setUpatedAt(?\DateTimeInterface $upatedAt): self
    {
        $this->upatedAt = $upatedAt;

        return $this;
    }

    public function getImagen(): ?int
    {
        return $this->imagen;
    }

    public function setImagen(?int $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }


}
