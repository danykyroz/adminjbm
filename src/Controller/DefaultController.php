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
      
  	   	$user=($this->getUser());

          if(!$user){
               return $this->redirect('login');
          }
          else{

            if($user->getRoles()[0]=="ROLE_ADMIN"){
                return $this->redirect('admin/home');
            }

          }


    
  }
}


