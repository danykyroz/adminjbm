<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Entity\Auxiliares;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

use App\Form\ClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\UserProviderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * @Route("/auxiliar")
 */

class AuxiliaresController extends AbstractController
{

    private $userManager;
    private $paginator;
    public $session;

    public function __construct(UserManagerInterface $userManager, PaginatorInterface $paginator,SessionInterface $session){
        $this->userManager=$userManager;
        $this->paginator=$paginator;
        $this->session=$session;
    } 

  /**
   * @Route("/index", name="auxiliar_index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
  	   	  $user=($this->getUser());

          $em=$this->getDoctrine()->getManager();

          $qb=$em->createQueryBuilder();
          $qb->select('a')->from('App:Auxiliares','a');


          //Busquedas
        if($request->get('query')!=""){

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'a', 'id'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'a', 'razonSocial'));
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }
      
        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
     
   

          return $this->render('clientes/auxiliares.html.twig',[
            'auxiliares' => $pagination,
            'pagination'=>$pagination,
            'query'=>$request->get('query',''),
          ]); 


    
  }

  /**
     * @Route("/new", name="auxiliares_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $auxiliar = new Auxiliares();

        $id=$request->get('id');
        $documento=$request->get('documento');
        $razonSocial=$request->get('razonSocial');
        $celular=$request->get('celular');
        $direccion=$request->get('direccion');
        $email=$request->get('email');
        
        if($id>0){
          $cliente=$em->getRepository('App:Clientes','c')->find($id);
        }
        $auxiliar->setDocumento($documento);
        $auxiliar->setEmail($email);
        $auxiliar->setRazonSocial($razonSocial);
        $auxiliar->setCelular($celular);
        $auxiliar->setDireccion($direccion);
        
        
        $em->persist($auxiliar);
        $em->flush();

        $rol="ROLE_AUXILIAR";
       
        if($id==0){
          
          $user=$this->createUserClient($auxiliar,$rol);    
         
          if(!$user){
              $em->remove($auxiliar);
              $em->flush();
              $this->addFlash('bad', 'Ya existe un auxiliar o usuario con este correo');
          }
          
        }
        
        
        if ($auxiliar->getId()>0) {
             
                $this->addFlash('success', 'Auxiliar creado exitosamente!');
                return $this->redirectToRoute('auxiliar_index');
        }

        return $this->redirectToRoute('auxiliar_index');
    }

  



    /**
     * @Route("/{id}/edit", name="clientes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Auxiliares $cliente): Response
    {
        $form = $this->createForm(ClientesType::class, $cliente);
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        $session=$this->session;

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($cliente);
            $em->flush();


            $this->addFlash('success', 'Perfil actualizado exitosamente.');

            return $this->redirectToRoute('clientes_edit',array('id'=>$cliente->getId()));
        }

        return $this->render('clientes/edit.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="auxiliar_delete", methods={"GET"})
     */
    public function delete(Request $request, Auxiliares $cliente): Response
    {
          
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->remove($cliente);
          $entityManager->flush();
          
          $this->addFlash('success', 'Auxiliar eliminado exitosamente.');


        return $this->redirectToRoute('auxiliar_index');
    }


    private function createUserClient($cliente,$rol){

            $userManager = $this->userManager;
            
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository("App:FosUser")->findOneBy(["email" => $cliente->getEmail()]);

            if (!$user) {
                /** @var  $user GrantUser */
                $expusername=explode("@",$cliente->getEmail());
                $username=$cliente->getRazonSocial();   
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

 

}


