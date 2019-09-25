<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\FlotillasClientes;

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
              return  $this->redirect($this->generateUrl('dashboard_flotilla'));
        }
        if($user->getRoles()[0]=="ROLE_GASOLINERA"){
              return  $this->redirect($this->generateUrl('dashboard_gasolinera'));
        }
        if($user->getRoles()[0]=="ROLE_CLIENTE"){
                return  $this->redirect($this->generateUrl('dashboard_cliente'));
        }
        if($user->getRoles()[0]=="ROLE_GASOLINERA"){
             return $this->render('home/index_gasolinera.html.twig');
        }
    }


    /**
     * @Route("/dashboard/cliente", name="dashboard_cliente")
     */
    public function dashboard_cliente(Request $request)
    {
        
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $session=$request->getSession();

        $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
        
        $wallet=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

        if($cliente->getAvatar()!=""){

            $session->set('avatar',$cliente->getAvatar());
        }

        $data=array('wallet'=>$wallet,'gasolina_diesel'=>0,
            'gasolina_premium'=>0,'creditos'=>0);

        return $this->render('home/index_cliente.html.twig',$data);
    }


     /**
     * @Route("/dashboard/gasolinera", name="dashboard_gasolinera")
     */
    public function dashboard_gasolinera(Request $request)
    {
        
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $session=$request->getSession();

        $gasolinera_usuario=$em->getRepository('App:GasolineraUsuarios','g')->findOneBy(array('usuarioId'=>$user->getId()));
        
        
        $gasolinera=$gasolinera_usuario->getGasolinera();

       
        $data=array('gasolinera'=>$gasolinera,'gasolina_diesel'=>0,
            'gasolina_premium'=>0);

        return $this->render('home/index_gasolinera.html.twig',$data);
    }

    /**
     * @Route("/dashboard/flotilla", name="dashboard_flotilla")
     */
    public function dashboard_flotilla(Request $request)
    {
        
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $session=$request->getSession();

        $flotilla_usuario=$em->getRepository('App:FlotillaUsuarios','f')->findOneBy(array('usuarioId'=>$user->getId()));
        
        $flotilla=$flotilla_usuario->getFlotilla();
        $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
         $wallet=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));


        $qb_clientes=$em->getRepository('App:FlotillasClientes')->listaClientesFlotilla($flotilla->getId());


        $total_clientes=count($qb_clientes->getQuery()->getResult());

        $saldo_clientes=$em->getRepository(FlotillasClientes::class)->getSaldoClientesFlotilla($flotilla->getId());

        $session->set("flotilla",$flotilla);
        
        $data=array('flotilla'=>$flotilla,
                    'wallet'=>$wallet,
                    'total_clientes'=>$total_clientes,
                    'saldo_clientes'=>$saldo_clientes,
                    'gasolina_diesel'=>0,
                    'gasolina_premium'=>0,);

        return $this->render('home/index_flotilla.html.twig',$data);
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