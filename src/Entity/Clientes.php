<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clientes
 *
 * @ORM\Table(name="clientes")
 * @ORM\Entity
 */
class Clientes
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
     * @var string|null
     *
     * @ORM\Column(name="documento", type="string", length=11, nullable=true)
     */
    private $documento;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombres", type="string", length=50, nullable=true)
     */
    private $nombres;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidos", type="string", length=50, nullable=true)
     */
    private $apellidos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="indicativo", type="integer", nullable=true)
     */
    private $indicativo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="celular", type="string", length=20, nullable=true)
     */
    private $celular;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pais", type="string", length=11, nullable=true)
     */
    private $pais;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="terminos", type="string", length=2, nullable=true)
     */
    private $terminos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="politicas", type="string", length=2, nullable=true)
     */
    private $politicas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(?string $documento): self
    {
        $this->documento = $documento;

        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(?string $nombres): self
    {
        $this->nombres = $nombres;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getIndicativo(): ?int
    {
        return $this->indicativo;
    }

    public function setIndicativo(?int $indicativo): self
    {
        $this->indicativo = $indicativo;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(?string $celular): self
    {
        $this->celular = $celular;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(?string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

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

    public function getTerminos(): ?string
    {
        return $this->terminos;
    }

    public function setTerminos(?string $terminos): self
    {
        $this->terminos = $terminos;

        return $this;
    }

    public function getPoliticas(): ?string
    {
        return $this->politicas;
    }

    public function setPoliticas(?string $politicas): self
    {
        $this->politicas = $politicas;

        return $this;
    }


}
