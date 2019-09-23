<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\UserProviderInterface;
use Knp\Component\Pager\PaginatorInterface;


class HelperController extends Controller
{

	public $userManager;
    public $paginator;
    public $managerInterface;
    public function __construct(UserManagerInterface $userManager,EntityManagerInterface $managerInterface, PaginatorInterface $paginator){
        $this->userManager=$userManager;
        $this->managerInterface=$managerInterface;
        $this->paginator=$paginator;
    }

	public function createUserClient($cliente,$rol){

            $userManager = $this->userManager;
            
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository("App:FosUser")->findOneBy(["email" => $cliente->getEmail()]);

            if (!$user) {
                /** @var  $user GrantUser */
                $expusername=explode("@",$cliente->getEmail());
                $username=$expusername[0];   
                $user = $userManager->createUser();
                $user->setUsername($username);
                //$user->setCreatedAt(new DateTime('now'));
                //$user->setUsernameCanonical($username);
                $user->setEmail($cliente->getEmail());
                //$user->setEmailCanonical($cliente->getEmail());
                $user->setEnabled(true);
                $user->setRoles(array($rol));
                $user->setPlainPassword($cliente->getDocumento());
                $userManager->updateUser($user);
                return true;
            }else{
                return false;
            }
    }

    public function generateToken($email){

        $token=\hash('sha256',$email.time().rand(0,100));
        return $token;
    }
}