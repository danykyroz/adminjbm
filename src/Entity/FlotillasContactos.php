<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FlotillasContactos
 *
 * @ORM\Table(name="flotillas_contactos")
 * @ORM\Entity
 */
class FlotillasContactos
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
     * @ORM\Column(name="contacto_id", type="integer", nullable=true)
     */
    private $contactoId;

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

    public function getContactoId(): ?int
    {
        return $this->contactoId;
    }

    public function setContactoId(?int $contactoId): self
    {
        $this->contactoId = $contactoId;

        return $this;
    }


}
