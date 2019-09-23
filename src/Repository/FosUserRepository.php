<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class FosUserRepository extends EntityRepository
{
    public function ListarUsuarios()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->select("f")
            ->where("f.roles LIKE :rol1 OR f.roles LIKE :rol2")
            ->andWhere("f.enabled = 1")
            ->orderBy("f.id", "ASC");
        $qb->setParameter('rol1',"%vendedor%");
        $qb->setParameter('rol2',"%gasolinera%");

        return $qb;
    }
}