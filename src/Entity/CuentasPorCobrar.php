<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Delegacion as Delegacion;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * CuentasPorCobrar
 *
 * @ORM\Table(name="cuentas_por_cobrar")
 * @ORM\Entity(repositoryClass="App\Repository\CuentasPorCobrarRepository")
 */
class CuentasPorCobrar
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
     * @var integer
     *
     * @ORM\Column(name="proveedor", type="string", length=255, nullable=false)
     */
    private $proveedor = '';

     /**
     * @var string
     *
     * @ORM\Column(name="rfc", type="string", length=20, nullable=true)
     */
    private $rfc = '';

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="string", length=20, nullable=true)
     */
    private $folio = '';

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", nullable=false)
     */
    private $valor = '';

    /**
     * @var float
     *
     * @ORM\Column(name="iva", type="float", nullable=false)
     */
    private $iva = '';

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=false)
     */
    private $total = '';

    
     /**
     * @var integer
     *
     * @ORM\Column(name="comprobado", type="integer", nullable=false)
     */
    private $comprobado = '';

     /**
     * @var string
     *
     * @ORM\Column(name="xml", type="string", nullable=false)
     */
    private $xml = '';

    /**
     * @var \DateTime|null
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

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

     /**
     * @var integer
     *
     * @ORM\Column(name="pago_id", type="integer", length=11, nullable=true)
     */
    private $pagoId = '';


    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", nullable=false)
     */
    private $code = '';

    /**
     * @var string
     *
     * @ORM\Column(name="cfdi", type="string", nullable=false)
     */
    private $cfdi = '';

     /**
     * @var string
     *
     * @ORM\Column(name="cancelable", type="string", nullable=false)
     */
    private $cancelable = '';

     /**
     * @var string
     *
     * @ORM\Column(name="estado_cancelacion", type="string", nullable=false)
     */
    private $estadoCancelacion = '';
    
    /**
     * @var string
     *
     * @ORM\Column(name="response_json", type="text", nullable=false)
     */
    private $responseJson = '';

     /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre = '';

     /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=3, nullable=false)
     */
    private $extension = '';

    /**
     * @var integer
     *
     * @ORM\Column(name="cliente_id", type="integer", nullable=false)
     */
    private $clienteId = '';


     /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=50, nullable=true)
     */
    private $uuid = '';


    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getFolio(): ?string
    {
        return $this->folio;
    }

    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getFacturado(): ?float
    {
        return $this->facturado;
    }

    public function setFacturado(float $facturado): self
    {
        $this->facturado = $facturado;

        return $this;
    }

    public function getPorFacturar(): ?float
    {
        return $this->porFacturar;
    }

    public function setPorFacturar(float $porFacturar): self
    {
        $this->porFacturar = $porFacturar;

        return $this;
    }

      public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha($fecha): self
    {
        $this->fecha = $fecha;

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

    public function getClienteId()
    {
        return $this->clienteId;
    }

    public function setClienteId(string $clienteId): self
    {
        $this->clienteId = $clienteId;

        return $this;
    }

    public function getProveedor(): ?string
    {
        return $this->proveedor;
    }

    public function setProveedor(string $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getRfc(): ?string
    {
        return $this->rfc;
    }

    public function setRfc(?string $rfc): self
    {
        $this->rfc = $rfc;

        return $this;
    }

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function setIva(float $iva): self
    {
        $this->iva = $iva;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getComprobado(): ?int
    {
        return $this->comprobado;
    }

    public function setComprobado(int $comprobado): self
    {
        $this->comprobado = $comprobado;

        return $this;
    }

    public function getXml(): ?string
    {
        return $this->xml;
    }

    public function setXml(string $xml): self
    {
        $this->xml = $xml;

        return $this;
    }

    public function getPagoId(): ?int
    {
        return $this->pagoId;
    }

    public function setPagoId(int $pagoId): self
    {
        $this->pagoId = $pagoId;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getCfdi(): ?string
    {
        return $this->cfdi;
    }

    public function setCfdi(string $cfdi): self
    {
        $this->cfdi = $cfdi;
        return $this;
    }

    public function getCancelable(): ?string
    {
        return $this->cancelable;
    }

    public function setCancelable(string $cancelable): self
    {
        $this->cancelable = $cancelable;
        return $this;
    }

    public function getEstadoCancelacion(): ?string
    {
        return $this->estadoCancelacion;
    }

    public function setEstadoCancelacion(string $estadoCancelacion): self
    {
        $this->estadoCancelacion = $estadoCancelacion;
        return $this;
    }

    public function getResponseJson(): ?string
    {
        return $this->responseJson;
    }

    public function setResponseJson(string $responseJson): self
    {
        $this->responseJson = $responseJson;
        return $this;
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

    public function getExtension(): ?string
    {
        return $this->extension;
    }


    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

     public function getUuid(): ?string
    {
        return $this->uuid;
    }


    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }
   

}
