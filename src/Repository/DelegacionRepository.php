<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class DelegacionRepository extends EntityRepository
{
    public function orderByName()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:Delegacion", "d")
            ->select("d")
            ->orderBy("d.municipio", "ASC");
        return $qb->getQuery()->getResult();
    }

    public function listarPorEstado($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:EstadosMunicipios", "e")
        	->innerJoin('App:Delegacion','d','WITH','e.municipiosId=d')
            ->select("d")
            ->where('e.estadosId=:id')
            ->setParameter('id',$id)
            ->orderBy("d.municipio", "ASC");
        return $qb->getQuery()->getResult();
    }



}