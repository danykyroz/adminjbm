<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\RegistroUsuariosFormType;

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
     * @Route("/user/chek_login", name="user_check_login")
    */
    public function check_login(Request $request)
    {
       
        $user = $this->userManager->createUser();
        $username=$request->get('_username'); 
        $em=$this->getDoctrine()->getManager();
        $password_correcto=false;

        $fosuser = $this->managerInterface->getRepository("App:User")->findOneBy(["username" => $username]);

        if(!$fosuser){

            $fosuser = $this->managerInterface->getRepository("App:User")->findOneBy(["email" => $username]);

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

            	$this->addFlash('bad', 'Article Created! Knowledge is power!');


            		return $this->render('/user/new.html.twig',['form'=>$form->createView()]);
            	}
               
    			$user->setRoles(array($role));
                $this->userManager->updateUser($user);
                
            }


		}

       return $this->render('/user/new.html.twig',['form'=>$form->createView()]);
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