<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\UserController;

use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Entity\Gasolineras;
use App\Entity\PuntosVenta;




/**
 * @Route("/vendedor")
 */

class AppVendedorController extends UserController
{

	  private $userManager;
    private $factory;
    private $tokenManager;
    private $managerInterface;
	
    public function __construct(UserManagerInterface $userManager, EncoderFactoryInterface $factory,CsrfTokenManagerInterface $tokenManager = null, EntityManagerInterface $managerInterface )
    {
        $this->userManager = $userManager;
        $this->factory=$factory;    
        $this->tokenManager = $tokenManager;  
        $this->managerInterface= $managerInterface;
    }


  /**
   * @Route("/", name="app_vendedor_index")
   */

  public function index(Request $request)
  {
  	 return $this->render('app_vendedor/index.html.twig'); 
  }

  /**
   * @Route("/configuracion", name="app_vendedor_configuracion")
   */

  public function configuracion(Request $request)
  {
    $user=($this->getUser());
    $em=$this->getDoctrine()->getManager();
      
    if($user->getRoles()[0]=="ROLE_GASOLINERA"){
      
      $gasolinera_user=$em->getRepository('App:GasolineraUsuarios','u')->findOneBy(array('usuarioId'=>$user->getId()));

      $gasolinera=$gasolinera_user->getGasolinera();

      $puntosVentas = $this->getDoctrine()
            ->getRepository(PuntosVenta::class)
            ->findByGasolinera($gasolinera);
 
      return $this->render('app_vendedor/configuracion.html.twig',['puntos'=>$puntosVentas]); 
    

    }
  }

  /**
   * @Route("/qr", name="app_vendedor_qr")
   */

  public function qr(Request $request)
  {
     return $this->render('app_vendedor/qr.html.twig'); 
  }

  /**
   * @Route("/qr/scan", name="app_vendedor_qr_scan")
  */

  public function qr_scanner(Request $request)
  {
    
     $qb = $this->getDoctrine()
            ->getRepository(Gasolineras::class)->orderById();

     $gasolineras=$qb->getQuery()->getResult();
     unset($gasolineras[0]);       
     return $this->render('app_vendedor/qr_scanner.html.twig'); 
  }


  /**
   * @Route("/recargar", name="app_vendedor_recargar")
   */

  public function recargar(Request $request)
  {
    
     $qb = $this->getDoctrine()
            ->getRepository(Gasolineras::class)->orderById();

     $gasolineras=$qb->getQuery()->getResult();
     unset($gasolineras[0]);       
     return $this->render('app_vendedor/recarga.html.twig'); 
  }





  /**
   * @Route("/gasolineras", name="app_vendedor_gasolineras")
   */

  public function gasolineras(Request $request)
  {
    
       $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();

      $gasolinera_usuario=$em->getRepository('App:GasolineraUsuarios','g')->findOneBy(array('usuarioId'=>$user->getId()));
        
        
        $gasolinera=$gasolinera_usuario->getGasolinera();

        $gasolineras[0]=$gasolinera;

     
     return $this->render('app_vendedor/gasolineras.html.twig',['gasolineras'=>$gasolineras]); 
  }

  /**
   * @Route("/gasolineras/detalle/{id}", name="app_vendedor_gasolinera_detalle")
   */

  public function gasolinera_detalle(Gasolineras $gasolinera, Request $request)
  {
    
    
     return $this->render('app_vendedor/gasolinera_detalle.html.twig',['gasolinera'=>$gasolinera]); 
  }

  /**
   * @Route("/transacciones", name="app_vendedor_transacciones")
   */

  public function transacciones(Request $request)
  {
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $session=$request->getSession();

        $gasolinera_usuario=$em->getRepository('App:GasolineraUsuarios','g')->findOneBy(array('usuarioId'=>$user->getId()));
        
        
        $gasolinera=$gasolinera_usuario->getGasolinera();

        $gasolinera->getId();
        
        $qb=$em->createQueryBuilder();
        $qb->select('t')->from('App:Transacciones','t')->where('t.gasolineraId=:gasolineraId');

        $qb->setParameter('gasolineraId',$gasolinera->getId());

        $transacciones=$qb->getQuery()->getResult();
       
        
     return $this->render('app_vendedor/transacciones.html.twig',['transacciones'=>$transacciones]); 
  }

  /**
   * @Route("/promociones", name="app_vendedor_promociones")
   */

  public function promociones(Request $request)
  {
     return $this->render('app_vendedor/promociones.html.twig'); 
  }


  /**
   * @Route("/recuperar/cuenta", name="app_recuperar_cuenta")
   */

  public function recuperar_cuenta(Request $request)
  {
     return $this->render('app_vendedor/recuperar_cuenta.html.twig'); 
  }

  /**
   * @Route("/slider", name="app_vendedor_slider")
  */

  public function slider(Request $request)
  {
		return $this->render('app_vendedor/slider.html.twig'); 
  }

  /**
   * @Route("/login", name="app_vendedor_login")
   */

  public function login(Request $request)
  {
  	  $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;


         $data=array(
            'last_username' => '',
            'error' => '',
            'csrf_token' => $csrfToken,
            );

  	 return $this->render('app_vendedor/login.html.twig',$data); 
  }

  /**
   * @Route("/profile", name="app_vendedor_profile")
   */

  public function profile(Request $request)
  {
      $user=($this->getUser());
      $em=$this->getDoctrine()->getManager();

      $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
        
     $data=array('cliente'=>$cliente);

     return $this->render('app_vendedor/profile.html.twig',$data); 
  }


  /**
   * @Route("/update/profile", name="app_vendedor_update_profile")
   */

  public function update_profile(Request $request)
  {
      $user=($this->getUser());
      $em=$this->getDoctrine()->getManager();

      $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
        
     $data=array('cliente'=>$cliente);

     $nombres=$request->get('nombres');
     $apellidos=$request->get('apellidos');
     $celular=$request->get('celular');
     $placa=$request->get('placa');

     $cliente->setNombres($nombres);
     $cliente->setApellidos($apellidos);
     $cliente->setCelular($celular);
     $cliente->setPlaca($placa);
     $em->persist($cliente);
     $em->flush();

      $this->addFlash('success', 'Perfil actualizado exitosamente.');

     return $this->render('app_vendedor/profile.html.twig',$data); 
  }

  /**
   * @Route("/dashboard", name="app_vendedor_dashboard")
  */

  public function dashboard(Request $request)
  {
		$punto_venta=$request->get('punto_venta');
    $session=$request->getSession();
    $session->set('punto_venta',$punto_venta);
    return  $this->redirect($this->generateUrl('dashboard_vendedor'));
  }


  /**
   * @Route("/logout", name="app_logout")
  */

  public function logout(Request $request)
  {
   
   $session=$request->getSession();
   $session->clear();
   return $this->render('app_vendedor/logout.html.twig'); 
  }




}