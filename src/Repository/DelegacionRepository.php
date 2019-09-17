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



}