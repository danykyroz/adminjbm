<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flotillas
 *
 * @ORM\Table(name="flotillas", indexes={@ORM\Index(name="contacto_id", columns={"contacto_id"})})
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
     * @ORM\Column(name="documento", type="integer", nullable=true)
     */
    private $documento;

    /**
     * @var \Contactos
     *
     * @ORM\ManyToOne(targetEntity="Contactos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contacto_id", referencedColumnName="id")
     * })
     */
    private $contacto;

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

    public function getDocumento(): ?int
    {
        return $this->documento;
    }

    public function setDocumento(?int $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getContacto(): ?Contactos
    {
        return $this->contacto;
    }

    public function setContacto(?Contactos $contacto): self
    {
        $this->contacto = $contacto;

        return $this;
    }


}
