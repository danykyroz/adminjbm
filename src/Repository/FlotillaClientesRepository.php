<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class FlotillaClientesRepository extends EntityRepository
{

    public function listaClientesFlotilla($flotillaId){

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:Clientes", "c")
            ->innerJoin('App:FlotillasClientes','fc','WITH','fc.clienteId=c')
            ->select("c")
            ->where('fc.flotillaId=:flotillaId')
            ->setParameter('flotillaId',$flotillaId);

        return $qb;


    }

    public function getSaldoClientesFlotilla($flotillaId)
    {
        $em = $this->getEntityManager();
        $qb=$em->createQueryBuilder();
        $qb->select('sum(w.saldo) as saldo')->from('App:Flotillas','f')
        ->innerJoin('App:FlotillasClientes', 'fc', 'WITH', 'fc.flotillaId = f')
        ->innerJoin('App:Clientes', 'c', 'WITH', 'fc.clienteId = c')
        ->innerJoin('App:Wallet', 'w', 'WITH', 'w.clienteId = c')
        ->where('f.id=:id')->setParameter('id',$flotillaId);
        return $qb->getQuery()->getSingleScalarResult();
    }
   
}