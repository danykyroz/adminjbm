<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class FlotillaUsuariosRepository extends EntityRepository
{
    public function SelectUsuariosFlotilla($flotillaId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->leftJoin('App:FlotillaUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f.id")
            ->addselect("f.email")
            ->addSelect("f.username")
            ->where("f.roles LIKE :roles")
            //->andWhere("u.flotillaId<>$flotillaId")
            ->andWhere('u.id is null')
            ->andWhere("f.enabled = 1")
            ->orderBy("f.id", "ASC");
        
        //$qb->setParameter('flotillaId',$flotillaId);
        $qb->setParameter('roles',"%flotilla%");

        return $qb->getQuery()->getResult();
    }


    public function listaUsuariosFlotilla($flotillaId){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->innerJoin('App:FlotillaUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f.id")
            ->addselect("f.email")
            ->addSelect("f.username")
            ->where("f.roles LIKE :roles")
            ->andWhere('u.flotillaId=:flotillaId')
            ->andWhere("f.enabled = 1")
            ->orderBy("f.id", "ASC");
        
        $qb->setParameter('flotillaId',$flotillaId);
        $qb->setParameter('roles',"%flotilla%");

        return $qb->getQuery()->getResult();


    }
}