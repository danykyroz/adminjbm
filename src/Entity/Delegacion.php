<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Delegacion
 *
 * @ORM\Table(name="delegacion")
 * @ORM\Entity(repositoryClass="App\Repository\DelegacionRepository")
 * @ORM\Entity
 */
class Delegacion
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
     * @var string
     *
     * @ORM\Column(name="municipio", type="string", length=100, nullable=false)
     */
    private $municipio;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMunicipio(): ?string
    {
        return utf8_decode($this->municipio);
    }

    public function setMunicipio(string $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function __toString(){
        return $this->getMunicipio();
    }

}
