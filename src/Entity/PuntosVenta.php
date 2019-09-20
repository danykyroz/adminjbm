<?php

namespace App\Entity;

use App\Entity\Gasolineras;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Flotillas
 *
 * @ORM\Table(name="puntos_venta")

 * @ORM\Entity
 */
class PuntosVenta
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
     * Many-to-one relationship between vendedor and puntos de ventas
     *
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="App\Entity\Gasolineras")
     * @ORM\JoinColumn(name="gasolinera_id", referencedColumnName="id")
    */
    private $gasolinera;
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

    public function getGasolinera(): ?Gasolineras
    {
        return $this->gasolinera;
    }

    public function setGasolinera(?Gasolineras $gasolinera): self
    {
        $this->gasolinera = $gasolinera;

        return $this;
    }

}
