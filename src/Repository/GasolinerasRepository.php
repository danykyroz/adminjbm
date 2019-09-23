<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class GasolinerasRepository extends EntityRepository
{
    public function orderById()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:Gasolineras", "g")
            ->select("g")
            ->orderBy("g.id", "ASC");
        return $qb;
    }

    public function SelectUsuariosGasolinera($GasolineraId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->leftJoin('App:GasolineraUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f.id")
            ->addselect("f.email")
            ->addSelect("f.username")
            ->where("f.roles LIKE :roles")
            //->andWhere("u.flotillaId<>$flotillaId")
            ->andWhere('u.id is null')
            ->andWhere("f.enabled = 1")
            ->orderBy("f.id", "ASC");

        //$qb->setParameter('flotillaId',$flotillaId);
        $qb->setParameter('roles',"%gasolinera%");

        return $qb->getQuery()->getResult();
    }

    public function listaUsuariosGasolinera($GasolineraId){

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->innerJoin('App:GasolineraUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f")
            ->where("f.roles LIKE :roles")
            ->andWhere("f.enabled = 1")
            ->andWhere('u.gasolineraId=:gasolineraId')
            ->setParameter('gasolineraId',$GasolineraId)
            ->setParameter('roles',"%gasolinera%");

        return $qb;


    }


}