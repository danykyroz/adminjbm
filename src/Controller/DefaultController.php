<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{


  /**
   * @Route("/", name="index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
  	   	  $user=($this->getUser());

          if(!$user){
               return $this->redirect('login');
          }
          else{

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->redirect('admin/home');
            }
            if($user->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
                 return $this->render('@EasyAdmin/home/index.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_CLIENTE"){
                 
                 $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));

                 if($cliente->getAvatar()!=""){
                  $session->set('avatar',$cliente->getAvatar());
                 }

                 return $this->render('@EasyAdmin/home/index_cliente.html.twig');
            }


          }
    
  }

 

  /**
   * @Route("/test", name="default_test")
   */
  public function test(Request $request){

       $em=$this->getDoctrine()->getManager();
       $user=$em->getRepository("App:FosUser")->find(1);
       var_dump($user->getUsername());
       die();

  }

  /**
   * @Route("/menu", name="default_menu")
   */
  public function menu(Request $request){

        $user=($this->getUser());

          if(!$user){
               return $this->redirect('login');
          }
          else{

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->render('home/menu_admin.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
                 return $this->render('home/menu_flotilla.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_GASOLINERA"){
                 return $this->render('home/menu_gasolinera.html.twig');
            }
            if($user->getRoles()[0]=="ROLE_CLIENTE"){
                 return $this->render('home/menu_cliente.html.twig');
            }

          }

  }

    /**
     * @Route("/admin/publicidad", name="publicidad")
     */
    public function publicidad(Request $request){

    return $this->render('home/publicidad.html.twig');

    }

    /**
     * @Route("/admin/promociones", name="promociones")
     */
    public function promociones(Request $request){

        return $this->render('home/promociones.html.twig');

    }

    /**
     * @Route("/admin/precio/gasolina", name="precio")
     */
    public function precio(Request $request){

        return $this->render('home/precio.html.twig');

    }
}


