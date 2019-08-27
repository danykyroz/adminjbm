<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flotillas
 *
 * @ORM\Table(name="flotillas")
 * @ORM\Entity
 */
class Flotillas
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
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fos_user_id", type="integer", nullable=true)
     */
    private $fosUserId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFosUserId(): ?int
    {
        return $this->fosUserId;
    }

    public function setFosUserId(?int $fosUserId): self
    {
        $this->fosUserId = $fosUserId;

        return $this;
    }


}
