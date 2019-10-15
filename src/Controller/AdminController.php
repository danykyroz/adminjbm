<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $session=$request->getSession();
        if($user->getAvatar()!=""){
            $session->set('avatar',$user->getAvatar());
        }
      
        if($user->getRoles()[0]=="ROLE_ADMIN"){
           return  $this->redirect($this->generateUrl('dashboard_admin'));
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
       
    }

    /**
     * @Route("/dashboard/admin", name="dashboard_admin")
    */
    public function dashboard_admin(Request $request){
       

        return $this->render('home/index_admin.html.twig');
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

        
        $qb=$em->createQueryBuilder();
        $qb->select('t')->from('App:Transacciones','t')->where('t.wallet=:wallet')->orderBy('t.createdAt','DESC');

        $qb->setParameter('wallet',$wallet);

        $transacciones=$qb->getQuery()->getResult();

        if($cliente->getAvatar()!=""){

            $session->set('avatar',$cliente->getAvatar());
        }

        $data=array('wallet'=>$wallet,'gasolina_diesel'=>0,
            'gasolina_premium'=>0,'creditos'=>0,'fecha'=>date('Y-m-d'),'transacciones'=>$transacciones);

        return $this->render('app/dashboard.html.twig',$data);
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

    /**
     * @Route("/admin/delegaciones", name="admin_delegaciones")
     */
    public function admin_delegaciones(Request $request)
    {
        
        $em=$this->getDoctrine()->getManager();
        $id=$request->get('id');
        $delegaciones=$em->getRepository('App:Delegacion')->listarPorEstado($id);

        $arr_delegaciones="";
       foreach($delegaciones as $delegacion){
        $id=$delegacion->getId();
        $nombre=$delegacion->getMunicipio();
        $arr_delegaciones.="<option value='$id'>$nombre</option>";
       }

       return new Response($arr_delegaciones);

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