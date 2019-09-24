<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends EasyAdminController
{
    /**
     * @Route("/", name="easyadmin")
     */
    public function index(Request $request)
    {
      
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $session=$request->getSession();
        
        if($user->getRoles()[0]=="ROLE_ADMIN"){
             return $this->render('@EasyAdmin/home/index.html.twig');
        }
        if($user->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
             return $this->render('home/index_flotilla.html.twig');
        }
        if($user->getRoles()[0]=="ROLE_CLIENTE"){
                 
                 $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));

                 if($cliente->getAvatar()!=""){
                  $session->set('avatar',$cliente->getAvatar());
                 }


             return $this->render('home/index_cliente.html.twig');
        }
        if($user->getRoles()[0]=="ROLE_GASOLINERA"){
             return $this->render('home/index_gasolinera.html.twig');
        }
    }

    protected function createNewPuntoVentaEntity()
	{
	    $flotilla = new Flotilla();
	    echo "aa";
	    die();
	    // ...

	    return $flotilla;
	}
}