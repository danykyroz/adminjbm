<?php

namespace App\Repository;

use App\Entity\CuentasPorCobrar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CuentasPorCobrarRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CuentasPorCobrar::class);
    }

    public function findCuentasPorCobrar()
    {
        return $this->createQueryBuilder('cc')
            ->where('cc.pagoId is null or cc.pagoId = 0')
            ->getQuery()->getResult()
            ;
    }

}
