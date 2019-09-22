<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;
//commit
class PuntosVentaUsuariosRepository extends EntityRepository
{
    public function SelectUsuariosPuntoVenta($puntoVenta)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->leftJoin('App:PuntosVentaUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f.id")
            ->addselect("f.email")
            ->addSelect("f.username")
            ->where("f.roles LIKE :roles")
            ->andWhere('u.id is null')
            ->andWhere("f.enabled = 1")
            ->orderBy("f.id", "ASC");
        $qb->setParameter('roles',"%vendedor%");

        return $qb->getQuery()->getResult();
    }


    public function listaUsuariosPuntoVenta($puntoVentaId){
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()->from("App:FosUser", "f")
            ->innerJoin('App:PuntosVentaUsuarios','u','WITH','f.id=u.usuarioId')
            ->select("f")
            ->where("f.roles LIKE :roles")
            ->andWhere("f.enabled = 1")
            ->andWhere('u.puntoventaId=:puntoventaId')
            ->setParameter('puntoventaId',$puntoVentaId)
            ->setParameter('roles',"%vendedor%");
        
        return $qb;


    }
}