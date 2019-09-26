<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class EstadosRepository extends EntityRepository
{
    public function orderByName()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:Estados", "e")
            ->select("e")
            ->orderBy("e.estado", "ASC");
        return $qb->getQuery()->getResult();
    }
    
}