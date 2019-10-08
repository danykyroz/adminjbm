<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{


  /**
   * @Route("/app", name="app_index")
   */

  public function index(Request $request)
  {
  	 return $this->render('app/index.html.twig'); 
  }

  /**
   * @Route("/app/splash", name="app_login")
   */

  public function login(Request $request)
  {
  	 return $this->render('landing/login.html.twig'); 
  }

  /**
   * @Route("/app/dashboard", name="app_dashboard")
   */

  public function dashboard(Request $request)
  {
		return $this->render('app/dashboard.html.twig'); 
  }


}