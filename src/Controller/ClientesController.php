<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\Wallet;
use App\Entity\FlotillasClientes;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

use App\Form\ClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\UserProviderInterface;

/**
 * @Route("/admin/clientes")
 */
class ClientesController extends AbstractController
{
    
    private $userManager;

    public function __construct(UserManagerInterface $userManager){
        $this->userManager=$userManager;
    }  

    /**
     * @Route("/", name="clientes_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        $user_admin=($this->getUser());
        $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));

        $flotilla_id=$flotilla_user->getFlotilla()->getId();
        $page=$request->get('page',1);

        if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
            $qb=$em->createQueryBuilder();
            $qb->select('c')->from('App:FlotillasClientes','f')
                            ->innerJoin('App:Clientes','c','WITH','f.clienteId=c')
                            ->where('f.flotillaId=:flotillaId');


            $qb->setParameter("flotillaId",$flotilla_id);

            $clientes=$qb->getQuery()->getResult();                
           
        }
        if($user_admin->getRoles()[0]=="ROLE_ADMIN"){
         $clientes = $this->getDoctrine()
            ->getRepository(Clientes::class)
            ->findAll();
        }
       
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta=new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(50);
        //$pagerfanta->setCurrentPage($page);

        return $this->render('clientes/index.html.twig', [
            'clientes' => $clientes,
            'pager_fanta'=>$pagerfanta
        ]);
    }

    /**
     * @Route("/new", name="clientes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cliente = new Clientes();
        $form = $this->createForm(ClientesType::class, $cliente);
        $form->handleRequest($request);
        $user_admin=($this->getUser());
        $entityManager = $this->getDoctrine()->getManager();
        $em=$this->getDoctrine()->getManager();

        $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));

        if ($form->isSubmitted() && $form->isValid()) {
            
            //Crear un usuario grant_user con clave = documento
            $tipo_cliente=$form->get('tipo')->getData();
            if($tipo_cliente==1){
                $rol="ROLE_CLIENTE";
            }
            if($tipo_cliente==2){
                $rol="ROLE_ADMIN_FLOTILLA";
            }

            $user=$this->createUserClient($cliente,$rol);    
            if(!$user){
                $this->addFlash('bad', 'Ya existe un cliente o usuario con este correo');
            }
            else{
                $entityManager->persist($cliente);
                $entityManager->flush();
                

                $wallet=new Wallet();
                $wallet->setCreatedAt(new \DateTime('now'));
                $wallet->setClienteId($cliente->getId());
                $wallet->setSaldo(0);
                $wallet->setQr(base64_encode($cliente->getId().$cliente->getDocumento().time()));

                $entityManager->persist($wallet);
                $entityManager->flush();


                if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
                  //Buscarmos dentro de lo user flotilla el id  
                    


                    if($flotilla_user){
                        $flotilla_id=$flotilla_user->getFlotilla()->getId();
                        $flotilla_cliente=new FlotillasClientes();
                        $flotilla_cliente->setClienteId($cliente->getId());
                        $flotilla_cliente->setFlotillaId($flotilla_id);
                        $entityManager->persist($flotilla_cliente);
                        $entityManager->flush();

                    }

                    
                }


                 $this->addFlash('success', 'Cliente creado exitosamente!');
                return $this->redirectToRoute('clientes_index');
            }
      
        }

        return $this->render('clientes/new.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="clientes_show", methods={"GET"})
     */
    public function show(Clientes $cliente): Response
    {
        $user_admin=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $wallet_flotilla=false;
        $wallet_cliente=false;

        if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){

        $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));
        
        $flotilla_cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user_admin->getEmail()));

        $wallet_flotilla=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$flotilla_cliente->getId()));
        
        $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

        }
        
        return $this->render('clientes/show.html.twig', [
            'cliente' => $cliente,
            'wallet_flotilla'=>$wallet_flotilla,
            'wallet_cliente'=>$wallet_cliente
        ]);
    }

     /**
     * @Route("/actualizar/saldo/{id}", name="cliente_actualizar_saldo", methods={"POST"})
     */
    public function actualizar_saldo(Clientes $cliente, Request $request): Response
    {
        $user_admin=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $wallet_flotilla=false;
        $wallet_cliente=false;

        if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){

        $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));
        
        $flotilla_cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user_admin->getEmail()));

        $wallet_flotilla=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$flotilla_cliente->getId()));
        
        $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));


        if($wallet_cliente->getId()==$request->get('wallet_cliente') and $wallet_flotilla->getId()==$request->get('wallet_flotilla') ){

            if($request->get('accion')=="agregar"){
                
                $wallet_cliente->setSaldo($wallet_cliente->getSaldo()+$request->get('valor'));

                $wallet_flotilla->setSaldo($wallet_flotilla->getSaldo()-$request->get('valor'));

              $this->addFlash('success', 'Saldo transferido exitosamente.');
   
            }
            if($request->get('accion')=="quitar"){

                $wallet_cliente->setSaldo($wallet_cliente->getSaldo()-$request->get('valor'));

                 $this->addFlash('success', 'Saldo actualizado exitosamente.');


            }

            $em->persist($wallet_cliente);
            $em->persist($wallet_flotilla);

            $em->flush();

           
            return $this->redirectToRoute('clientes_show',array('id'=>$cliente->getId()));

        }

        }
        
        return $this->render('clientes/show.html.twig', [
            'cliente' => $cliente,
            'wallet_flotilla'=>$wallet_flotilla,
            'wallet_cliente'=>$wallet_cliente
        ]);
    }
    

    /**
     * @Route("/{id}/edit", name="clientes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Clientes $cliente): Response
    {
        $form = $this->createForm(ClientesType::class, $cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('clientes_index');
        }

        return $this->render('clientes/edit.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="clientes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Clientes $cliente): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cliente->getId(), $request->request->get('_token'))) {
        
            $entityManager = $this->getDoctrine()->getManager();

            $user = $entityManager->getRepository("App:FosUser")->findOneBy(["email" => $cliente->getEmail()]);

            $entityManager->remove($user);

            $entityManager->remove($cliente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('clientes_index');
    }

    private function createUserClient($cliente,$rol){

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
}
