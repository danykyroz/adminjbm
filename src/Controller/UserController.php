<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\RegistroUsuariosFormType;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserController extends Controller
{
    
	private $userManager;
    
	 public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
                
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


}