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
       return $this->render('@EasyAdmin/home/index.html.twig');
    }

    protected function createNewFlotillaEntity()
	{
	    $flotilla = new Flotilla();
	    echo "aa";
	    die();
	    // ...

	    return $flotilla;
	}
}