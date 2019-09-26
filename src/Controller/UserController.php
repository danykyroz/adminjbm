<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\RegistroUsuariosFormType;
use App\Entity\FosUser;
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
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UserController extends Controller
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
     * @Route("/login", name="login")
    */
    public function login(Request $request)
    {
        
        $user=($this->getUser());
        if(is_object($user)){
            return $this->redirect('admin/home');
        }

         $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;


         $data=array(
            'last_username' => '',
            'error' => '',
            'csrf_token' => $csrfToken,
            );
            return $this->render('@FOSUser/Security/login.html.twig', $data);
        
    }

   /**
   * @Route("/recuperar/cuenta", name="recuperar_cuenta")
   */
    public function recuperar_cuenta(Request $request){

            
            $em=$this->getDoctrine()->getManager();
            
            $email=$request->get('_username','');
            $submit=$request->get('submit',false);

            $em=$this->getDoctrine()->getManager();
            $password_correcto=false;

            $fosuser = $this->managerInterface->getRepository("App:User")->findOneBy(["email" => $email]);

            $url_login = $this->generateUrl('login');

            if($fosuser){
            
            if(!$submit || $submit==""){

                $this->addFlash('success', "Se ha enviado un email a: $email, con las intrucciones para recuperar la contraseña");

                 $token=$this->generateToken($email);
                 $fosuser->setConfirmationToken($token);
                 $fosuser->setPasswordRequestedAt(new \DateTime('now'));
                 
                 $this->userManager->updateUser($fosuser);
                 $host=$request->getschemeAndHttpHost();
                 $arrContextOptions=array(
                 "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                 
                 $url = $this->generateUrl('email_recuperar_cuenta', array('email' => $fosuser->getEmail()));

                  $send_mail=file_get_contents($host.$url, false, stream_context_create($arrContextOptions));

            }else{

                $password=$request->get('_password');
                $password_check=$request->get('_password_check');
                    
                if(strtolower($password)==strtolower($password_check)){
                    $fosuser->setPlainPassword($password_check);
                    $this->userManager->updateUser($fosuser);

                    $this->addFlash('success', 'Contraseña reseteada exitosamente');
                    
                    return $this->redirect($url_login);

                }else{
                        
                    $this->addFlash('bad', 'password no coinciden'); 

                     return $this->render('/user/reset_cuenta.html.twig',array('email'=>$fosuser->getEmail(),'token'=>$token));
                }
            }
             
           
            }else{
                
                if($email!=""){
                    $this->addFlash('bad', 'usuario no existe en el sistema');
                    $this->redirect($url_login);
                }
            }  

            return $this->render('/user/recuperar_cuenta.html.twig',array('email'=>$email));
    }

    /**
   * @Route("/reset/cuenta/{token}", name="reset_cuenta")
   */
    public function reset_cuenta($token, Request $request){

            
            $em=$this->getDoctrine()->getManager();
            
            $token=$request->get('token',''); 
            $em=$this->getDoctrine()->getManager();
            $password_correcto=false;
            $url_login = $this->generateUrl('login');

            $fosuser = $this->managerInterface->getRepository("App:User")->findOneBy(["confirmationToken" => $token]);

            if($fosuser){
                
                return $this->render('/user/reset_cuenta.html.twig',array('email'=>$fosuser->getEmail(),'token'=>$token));
              
            }else{
                echo "no encontre usuario";
                $this->addFlash('bad', 'token no existe en el sistema');
                return $this->redirect($url_login);
            }

    }   



    /**
     * @Route("/user/chek_login", name="user_check_login")
    */
    public function check_login(Request $request)
    {
       
        $user = $this->userManager->createUser();
        $email=$request->get('_username'); 
        $em=$this->getDoctrine()->getManager();
        $password_correcto=false;

        $fosuser = $this->managerInterface->getRepository("App:User")->findOneBy(["email" => $email]);

        if(!$fosuser){

        }else{
            $password=$request->get('_password');
            $encoder = $this->factory->getEncoder($fosuser);
            
                    if ($encoder->isPasswordValid($fosuser->getPassword(),
                        $password,
                        $fosuser->getSalt())) {
                        $status = true;
                        $message = "Login satisfactorio";
                        $this->loginUser($fosuser, $request);

                            if ($request->getSession()->get('_security.main.target_path') != "") {
                                $redirect = $request->getSession()->get('_security.main.target_path');
                            } else {
                                $redirect = '/admin/home';
                            }
                        return $this->redirect($redirect);

                    } else {
                        $message = "Contraseña invalida";
                        $fosuser=false;
                    }
         


        }

        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

         $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        if(!$fosuser || !$password_correcto){

             $this->addFlash('bad', 'usuario o contraseña invalida');

            $data=array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            );
            return $this->render('@FOSUser/Security/login.html.twig', $data);
            //return $this->renderLogin();
        }  
    }


    /**
     * @Route("/admin/new/user", name="admin_new_user")
     */
    
    public function new(Request $request)
    {
       
    	$user = $this->userManager->createUser();
        $user->setEnabled(true);
        $form = $this->createForm(RegistroUsuariosFormType::class);

       return $this->render('/user/new.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("/admin/save/user", name="admin_save_user")
     */
    public function save_user(Request $request)
    {
       
    	$user = $this->userManager->createUser();
        $user->setEnabled(true);
        $em=$this->getDoctrine()->getManager();

        $form = $this->createForm(RegistroUsuariosFormType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                

            	$role = $form->get('roles')->getData();
    			$username= $form->get('username')->getData();

            	$existe=$em->getRepository('App:User')->findOneBy(array('username'=>$username));

            	if($existe){

            	$this->addFlash('bad', 'usuario ya existe!');


            		return $this->render('/user/new.html.twig',['form'=>$form->createView()]);
            	}
               
    			$user->setRoles(array($role));
                $user->setPlainPassword('permergas123456');
                $this->userManager->updateUser($user);

                $host=$request->getschemeAndHttpHost();
                 $arrContextOptions=array(
                 "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                 
                 $url = $this->generateUrl('email_nueva_cuenta', array('email' => $user->getEmail()));

                  $send_mail=file_get_contents($host.$url, false, stream_context_create($arrContextOptions));
                
            }


		}

       return $this->redirect($this->generateUrl('usuarios_index'));
    }
    
    private function generateToken($email){

        $token=\hash('sha256',$email.time().rand(0,100));
        return $token;
    }
    

    /**
     * @param $user GrantUser
     * @param $request
     */
    private function loginUser($user, $request)
    {
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->get('security.token_storage')->setToken($token); //now the user is logged in
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }


}