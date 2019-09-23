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
}