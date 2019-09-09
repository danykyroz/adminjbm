<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlotillaUsuarios
 *
 * @ORM\Table(name="flotilla_usuarios")
 * @ORM\Entity(repositoryClass="App\Repository\FlotillaUsuariosRepository")
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
     * @ORM\Column(name="flotilla_id", type="integer", nullable=true)
     */
    private $flotillaId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="usuario_id", type="integer", nullable=true)
     */
    private $usuarioId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsuarioId(): ?int
    {
        return $this->usuarioId;
    }

    public function setUsuarioId(?int $usuarioId): self
    {
        $this->usuarioId = $usuarioId;

        return $this;
    }


}
