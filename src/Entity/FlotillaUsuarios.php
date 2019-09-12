<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlotillaUsuarios
 *
 * @ORM\Table(name="flotilla_usuarios", indexes={@ORM\Index(name="flotilla_id", columns={"flotilla_id"})})
 * @ORM\Entity
 */
class FlotillaUsuarios
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
     * @var \Flotillas
     *
     * @ORM\ManyToOne(targetEntity="Flotillas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flotilla_id", referencedColumnName="id")
     * })
     */
    private $flotilla;

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

    public function getFlotilla(): ?Flotillas
    {
        return $this->flotilla;
    }

    public function setFlotilla(?Flotillas $flotilla): self
    {
        $this->flotilla = $flotilla;

        return $this;
    }


}
