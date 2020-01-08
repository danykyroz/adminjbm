<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Delegacion as Delegacion;
use Gedmo\Mapping\Annotation as Gedmo;
/**
 * Pagos
 *
 * @ORM\Table(name="pagos")
 * @ORM\Entity
 */
class Pagos
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
     * @ORM\Column(name="tipo", type="string", length=11, nullable=false)
     */
    private $tipo = '';

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="string", length=20, nullable=true)
     */
    private $folio = '';


    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=255, nullable=true)
     */
    private $file = '';


    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", nullable=false)
     */
    private $valor = '';

    /**
     * @var float
     *
     * @ORM\Column(name="facturado", type="float", nullable=false)
     */
    private $facturado = '';

     /**
     * @var float
     *
     * @ORM\Column(name="por_facturar", type="float", nullable=false)
     */
    private $porFacturar = '';

    /**
     * @var \DateTime|null
    * @ORM\Column(name="fecha", type="datetime", nullable=false)
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
     * @ORM\Column(name="cliente_id", type="string", length=11, nullable=false)
     */
    private $clienteId = '';

     /**
     * @var integer
     *
     * @ORM\Column(name="revisado", type="integer", length=11, nullable=true)
     */
    private $revisado = 0;
    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo($tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getFolio(): ?string
    {
        return $this->folio;
    }

    public function setFolio($folio): self
    {
        $this->folio = $folio;

        return $this;
    }

     public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getFacturado(): ?float
    {
        return $this->facturado;
    }

    public function setFacturado($facturado): self
    {
        $this->facturado = $facturado;

        return $this;
    }

    public function getPorFacturar(): ?float
    {
        return $this->porFacturar;
    }

    public function setPorFacturar($porFacturar): self
    {
        $this->porFacturar = $porFacturar;

        return $this;
    }

     public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
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

    public function getClienteId(): ?string
    {
        return $this->clienteId;
    }

    public function setClienteId(string $clienteId): self
    {
        $this->clienteId = $clienteId;

        return $this;
    }

    public function getRevisado()
    {
        return $this->revisado;
    }

    public function setRevisado($revisado): self
    {
        $this->revisado = $revisado;

        return $this;
    }

    public function consecutivo(){
        
        $prefijo="";
        $pad= str_pad($this->id,3,'0',STR_PAD_LEFT);

        if($this->tipo==2){
            $prefijo="tr";
            $folio=date_format($this->createdAt,'dmy');

        }else{
            $prefijo="ch";
            $folio=$this->getFolio();
        }

        return $prefijo.$folio.$pad;

    }

    public static function getMes($mes) {
        $meses = [
            '01' => 'Ene',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Sept',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dic',
        ];
        return $meses[$mes];
    }

   

}
