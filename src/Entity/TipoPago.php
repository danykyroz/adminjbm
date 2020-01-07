<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tipo_pago")
 */
class TipoPago  
{
    

       /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=50, nullable=false)
     */
    private $nombre;



    public function getId()
    {
        return $this->id;
    }

   
    public function getNombre(): ?string
    {
        return $this->nombre;
    }


    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

}