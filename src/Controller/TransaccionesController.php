<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin/transacciones")
*/

class TransaccionesController extends Controller
{

/**
* @Route("/", name="transacciones_index", methods={"GET"})
*/
public function index(Request $request)
{
	
	$em=$this->getDoctrine()->getManager();

	$qb=$em->createQueryBuilder();
    
    $transacciones=$qb->select('t')->from('App:Transacciones','t')->getQuery()->getResult();
           
    $data=array('transacciones'=>$transacciones);
	return $this->render('transacciones/index.html.twig',$data);
}

/**
* @Route("/movimientos", name="transacciones_movimientos", methods={"GET"})
*/
public function movimientos(Request $request)
{
	
	$em=$this->getDoctrine()->getManager();

	$qb=$em->createQueryBuilder();
    
    $movimientos=$qb->select('m')->from('App:Movimientos','m')->getQuery()->getResult();
           
    $data=array('movimientos'=>$movimientos);
	return $this->render('movimientos/index.html.twig',$data);
}	

}