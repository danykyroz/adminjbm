<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{


  /**
   * @Route("/", name="index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
          $em=$this->getDoctrine()->getManager();
  	   	  $user=($this->getUser());

          if(!$user){
               return $this->redirect('login');
          }
          else{

            $tclientes=$em->getRepository('App:Clientes')->findAll();
            $tauxiliares=$em->getRepository('App:Auxiliares')->findAll();
            $tarchivos=$em->getRepository('App:CuentasPorCobrar')->findAll();
            
            $clientes=count($tclientes);
            $auxiliares=count($tauxiliares);
            $archivos=count($tarchivos);

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->render('index.html.twig',array('clientes'=>$clientes,'auxiliares'=>$auxiliares,'archivos'=>$archivos));
            }
            if($user->getRoles()[0]=="ROLE_AUXILIAR"){

                 return $this->render('index.html.twig',array('clientes'=>$clientes,'auxiliares'=>$auxiliares,'archivos'=>$archivos));
            }
            if($user->getRoles()[0]=="ROLE_CLIENTE"){
                  return $this->render('index.html.twig',array('clientes'=>$clientes,'auxiliares'=>$auxiliares,'archivos'=>$archivos));
            }


          }
          
    
  }


  /**
   * @Route("/menu", name="default_menu")
   */
  public function menu(Request $request){

        $user=($this->getUser());
        
        $em=$this->getDoctrine()->getManager();

          if(!$user){
               return $this->redirect('login');
          }

          else{

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->render('home/menu_admin.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_AUXILIAR"){
                 return $this->render('home/menu_auxiliar.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_CLIENTE"){
                 return $this->render('home/menu_cliente.html.twig');
            }

          }

  }


  /**
   * @Route("/", name="landing")
   */

  public function landing(Request $request)
  {
      // en index pagina con datos generales de la app
        return $this->render('index.html.twig');
  }


}


