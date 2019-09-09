<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadosMunicipios
 *
 * @ORM\Table(name="estados_municipios", uniqueConstraints={@ORM\UniqueConstraint(name="estados_id", columns={"estados_id", "municipios_id"})}, indexes={@ORM\Index(name="municipios_id_refs_id_6d8b23ec", columns={"municipios_id"})})
 * @ORM\Entity
 */
class EstadosMunicipios
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
     * @var int
     *
     * @ORM\Column(name="estados_id", type="integer", nullable=false)
     */
    private $estadosId;

    /**
     * @var int
     *
     * @ORM\Column(name="municipios_id", type="integer", nullable=false)
     */
    private $municipiosId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstadosId(): ?int
    {
        return $this->estadosId;
    }

    public function setEstadosId(int $estadosId): self
    {
        $this->estadosId = $estadosId;

        return $this;
    }

    public function getMunicipiosId(): ?int
    {
        return $this->municipiosId;
    }

    public function setMunicipiosId(int $municipiosId): self
    {
        $this->municipiosId = $municipiosId;

        return $this;
    }


}
