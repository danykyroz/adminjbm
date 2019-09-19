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

        if($user->getRoles()[0]=="ROLE_ADMIN"){
             return $this->render('@EasyAdmin/home/index.html.twig');
        }
        if($user->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
             return $this->render('home/index_flotilla.html.twig');
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