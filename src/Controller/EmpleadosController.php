<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/clientes/empleados")
 */

class EmpleadosController extends AbstractController
{


  /**
   * @Route("/", name="index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
  	   	  $user=($this->getUser());


          return $this->render('empleados/index.html.twig'); 


          /*if(!$user){
               return $this->redirect('landing');
          }
          else{

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->redirect('admin/home');
            }
            if($user->getRoles()[0]=="ROLE_AUXILIARES"){
                 return $this->render('home/index.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_CONTADOR"){
                 
                
                 return $this->render('home/index_contador.html.twig');
            }


          }
          */
    
  }

  public function cliente(Request $request, $cliente){

   return $this->render('archivos/carpetas.html.twig'); 
  }

  public function cliente_mes_xml(Request $request,$cliente,$mes){

   return $this->render('archivos/mes.html.twig'); 
  
  }



}


