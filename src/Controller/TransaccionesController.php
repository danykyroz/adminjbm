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
	return $this->render('transacciones/index.html.twig');
}
}