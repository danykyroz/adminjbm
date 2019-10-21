<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\Wallet;
use App\Entity\FlotillasClientes;
use App\Entity\Transacciones AS Transaccion;
use App\Entity\Movimientos AS Movimiento;
use App\Entity\MovimientoSaldos;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

use App\Form\ClientesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\UserProviderInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/admin/clientes")
 */
class ClientesController extends AbstractController
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
     * @Route("/", name="clientes_index", methods={"GET"})
    */
    public function index(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        $user_admin=($this->getUser());

        if($user_admin->getRoles()[0]=="ROLE_CLIENTE"){
         return $this->redirectToRoute('perfil');
        }
        if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){
            
            $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));

            $flotilla_id=$flotilla_user->getFlotilla()->getId();
            $page=$request->get('page',1);


            $qb=$em->createQueryBuilder();
            $qb->select('c')->from('App:FlotillasClientes','f')
                            ->innerJoin('App:Clientes','c','WITH','f.clienteId=c')
                            ->where('f.flotillaId=:flotillaId');


            $qb->setParameter("flotillaId",$flotilla_id);

            $clientes=$qb->getQuery()->getResult();    

           
           
        }
        if($user_admin->getRoles()[0]=="ROLE_ADMIN"){
            $qb=$em->createQueryBuilder();
            $qb->select('c')->from('App:Clientes','c');
            
        }
        if($request->get('flotilla')!=""){

            $qb=$em->createQueryBuilder();
            $qb->select('c')->from('App:FlotillasClientes','f')
                            ->innerJoin('App:Clientes','c','WITH','f.clienteId=c')
                            ->where('f.flotillaId=:flotillaId');


            $qb->setParameter("flotillaId",$request->get('flotilla'));

        }
        //Busquedas
        if($request->get('query')!=""){

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'nombres'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'apellidos'));

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'documento'));
                
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'email'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'celular'));
            
             $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'placa'));
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }

        if($request->get('tipo_cliente')!=""){
            $qb->andWhere('c.tipo=:tipo')
            ->setParameter('tipo',$request->get('tipo_cliente'));
        }
        if($request->get('delegacion')!=""){

           $delegacion=$em->getRepository('App:Delegacion')->find($request->get('delegacion'));

            $qb->andWhere('c.delegacion=:delegacion')
            ->setParameter('delegacion',$delegacion);
        }

        if($request->get('estado')!=""){

          $qb->andWhere('c.estado=:estado')
            ->setParameter('estado',$request->get('estado'));
        }

        $delegaciones=$em->getRepository('App:Delegacion')->orderByName();
        $estados=$em->getRepository('App:Estados')->orderByName();
        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
        
        $lista_clientes=array();
        foreach ($pagination as $key => $cliente) {
             $lista_clientes[]=array('row'=>$cliente,'flotilla'=>$this->getFlotillaCliente($cliente->getId()));
        } 

        $flotillas=$em->getRepository('App:Flotillas','f')->findAll();
        //$pagerfanta->setCurrentPage($page);

        return $this->render('clientes/index.html.twig', [
            'clientes' => $lista_clientes,
            'pagination'=>$pagination,
            'query'=>$request->get('query',''),
            'tipo'=>array('1'=>'Cliente Wallet','2'=>'Admin Flotilla'),
            'delegaciones'=>$delegaciones,
            'tipo_cliente'=>$request->get('tipo_cliente',""),
            'delegacion_form'=>$request->get('delegacion',""),
            'estados'=>$estados,
            'estados_form'=>$request->get('estados',""),
            'estado'=>$request->get('estado',"1"),
            'flotillas'=>$flotillas,
            'flotilla_form'=>$request->get('flotilla',"")
        ]);
    }

    private function getFlotillaCliente($id){

        $em=$this->getDoctrine()->getManager();
        $qb=$em->createQueryBuilder();
        $qb->select('fc')->from('App:FlotillasClientes','fc')
        ->innerJoin('App:Clientes', 'c', 'WITH', 'fc.clienteId = c')
        ->where('fc.clienteId=:id')->setParameter('id',$id);
        $result= $qb->getQuery()->getResult();
        if(is_array($result) && count($result)>0){
             $flotilla=$em->getRepository('App:Flotillas','f')->find($result[0]->getFlotillaId());   
             return $flotilla->getNombre();
        }else{
            return 'NA';
        }

       
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
                //Enviar mail
                 $fosuser = $entityManager->getRepository("App:User")->findOneBy(["email" => $cliente->getEmail()]);

                 $host=$request->getschemeAndHttpHost();
                 $arrContextOptions=array(
                 "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                 
                 $url = $this->generateUrl('email_nueva_cuenta', array('email' => $fosuser->getEmail()));

                  $send_mail=file_get_contents($host.$url, false, stream_context_create($arrContextOptions));



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
   * @Route("/cambiar/password", name="cambiar_password")
   */
    public function cambiar_password(Request $request){

            
            $em=$this->getDoctrine()->getManager();
            
            $fosuser=($this->getUser());
            $submit=$request->get('submit');
            
            $password=$request->get('_password');
            $password_check=$request->get('_password_check');
                
            if(strtolower($password)==strtolower($password_check)){
                $fosuser->setPlainPassword($password_check);
                $this->userManager->updateUser($fosuser);

                $this->addFlash('success', 'Contraseña reseteada exitosamente');
                
               

            }else{
                    
                $this->addFlash('bad', 'password no coinciden'); 

               
            }  

           return $this->redirect($this->generateUrl('perfil'));
    }


    /**
     * @Route("/perfil", name="perfil", methods={"GET"})
     */
    public function perfil(Request $request): Response
    {
        $user_admin=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        $wallet_flotilla=false;
        $wallet_cliente=false;
        
        $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user_admin->getEmail()));

        if($user_admin->getRoles()[0]=="ROLE_ADMIN_FLOTILLA"){

            $flotilla_user=$em->getRepository('App:FlotillaUsuarios','u')->findOneBy(array('usuarioId'=>$user_admin->getId()));
            
            $flotilla_cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user_admin->getEmail()));
            
            if($flotilla_cliente){
                $wallet_flotilla=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$flotilla_cliente->getId()));
            }
        
        }

        if($cliente){

           $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));
     
        }else{
            $cliente=false;
        }
        
        return $this->render('clientes/perfil.html.twig', [
            'cliente' => $cliente,
            'wallet_flotilla'=>$wallet_flotilla,
            'wallet_cliente'=>$wallet_cliente
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
        
       
        }
        
         $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

         $flotilla=$this->getFlotillaCliente($cliente->getId());
 
        return $this->render('clientes/show.html.twig', [
            'cliente' => $cliente,
            'wallet_flotilla'=>$wallet_flotilla,
            'wallet_cliente'=>$wallet_cliente,
            'flotilla'=>$flotilla
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

        $saldo_anterior=$wallet_cliente->getSaldo();

        if($wallet_cliente->getId()==$request->get('wallet_cliente') and $wallet_flotilla->getId()==$request->get('wallet_flotilla') ){

            if($request->get('accion')=="agregar"){
                
                $valor=$request->get('valor');

                $wallet_cliente->setSaldo($wallet_cliente->getSaldo()+$request->get('valor'));

                $wallet_flotilla->setSaldo($wallet_flotilla->getSaldo()-$request->get('valor'));

              $this->addFlash('success', 'Saldo transferido exitosamente.');
                $operacion="suma";
 
            }

            if($request->get('accion')=="quitar"){

                $wallet_cliente->setSaldo($wallet_cliente->getSaldo()-$request->get('valor'));

                $wallet_flotilla->setSaldo($wallet_flotilla->getSaldo()+$request->get('valor'));

                 $this->addFlash('success', 'Saldo actualizado exitosamente.');

                 $operacion="resta";

            }

            $em->persist($wallet_cliente);
            $em->persist($wallet_flotilla);
            $em->flush();

            $this->crearTransaccion($saldo_anterior,$valor,$operacion,$wallet_cliente,$request->getClientIp());

            
            return $this->redirectToRoute('clientes_show',array('id'=>$cliente->getId()));

        }

        }
        

        //Si es super admin
        if($user_admin->getRoles()[0]=="ROLE_ADMIN"){

        
        $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

        $saldo_anterior=$wallet_cliente->getSaldo();

        if($wallet_cliente->getId()==$request->get('wallet_cliente')){

            if($request->get('accion')=="agregar"){
                
                if($request->get('escredito')==0){
                    $wallet_cliente->setSaldo($wallet_cliente->getSaldo()+$request->get('valor'));
                }

                if($request->get('escredito')==1){
                    $wallet_cliente->setSaldoCredito($wallet_cliente->getSaldoCredito()+$request->get('valor'));
                }

                $this->addFlash('success', 'Saldo transferido exitosamente.');
                
                $operacion="suma";
                $valor=$request->get('valor');
           
            }

            if($request->get('accion')=="quitar"){

                if($request->get('escredito')==1){
                    $wallet_cliente->setSaldoCredito($wallet_cliente->getSaldoCredito()-$request->get('valor'));
                }else{
                    $wallet_cliente->setSaldo($wallet_cliente->getSaldo()-$request->get('valor'));
                }
                
                $this->addFlash('success', 'Saldo actualizado exitosamente.');

                 $operacion="resta";
                 $valor=$request->get('valor');
            }

            $this->crearTransaccion($saldo_anterior,$valor,$operacion,$wallet_cliente,$request->getClientIp());

            $em->persist($wallet_cliente);
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
        $em=$this->getDoctrine()->getManager();
        $session=$this->session;

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $file = $form['avatar']->getData();
            if($file){
             $name='perfil'.$cliente->getId().'.'.$file->getClientOriginalExtension();
            $path_image='uploads/'.$name;
            $name_image='uploads/'.$name;
            $file->move('uploads/',$name);
            $cliente->setAvatar($name_image);
            $em->persist($cliente);
            $em->flush();
     
            }
           
            $session->set('avatar',$cliente->getAvatar());

            $this->addFlash('success', 'Perfil actualizado exitosamente.');

            return $this->redirectToRoute('clientes_edit',array('id'=>$cliente->getId()));
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

            if($user){
                $entityManager->remove($user);
            }
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
                $username=$cliente->getNombres().' '.$cliente->getApellidos();   
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


    public function crearTransaccion($saldo_anterior,$valor,$operacion,$wallet,$ip){
        

        $em = $this->getDoctrine()->getManager();
        $tipo_movimiento=1;
        $fos_user=$em->getRepository('App:FosUser','f')->find($this->getUser()->getId());
        if($operacion=='resta'){
            $valor=-$valor;
            $tipo_movimiento=2;
        }
        
        $obj_tipo_movimiento=$em->getRepository('App:TipoMovimientos','t')->find($tipo_movimiento);

        if($operacion=="suma"){

            $tr=new Transaccion();
            $tr->setCreatedAt(new \DateTime('now'));
            $tr->setUpdatedAt(new \DateTime('now'));
            $tr->setWallet($wallet);
            $tr->setValor($valor);
            $tr->setGasolineraId(1);
            $tr->setTipoTransaccion(1);
            $tr->setUsuarioId($this->getUser()->getId());
            $tr->setEstado('Aceptada');
            $tr->setRespuesta('Aprobada');
            $tr->setCodRespuesta(00);
            $tr->setIp($ip);
            $tr->setDispositivo('admin');

            $em->persist($tr);
            $em->flush();

        }
        


        $movimiento=new Movimiento();
        $movimiento->setWallet($wallet);
        $movimiento->setCreatedAt(new \DateTime('now'));
        $movimiento->setUpdatedAt(new \DateTime('now'));
        $movimiento->setValor($valor);
        $movimiento->setTipoMovimiento($obj_tipo_movimiento);
        $movimiento->setSincronizado(1);
        $movimiento->setFosUser($fos_user);
        $em->persist($movimiento);
        $em->flush();

        $movimiento_saldo=new MovimientoSaldos();
        $movimiento_saldo->setCreatedAt(new \DateTime('now'));
        $movimiento_saldo->setUpdatedAt(new \DateTime('now'));
        $movimiento_saldo->setMovimiento($movimiento);
        $movimiento_saldo->setSaldoAnterior($saldo_anterior);
        $movimiento_saldo->setValor($valor);
        $movimiento_saldo->setNuevoSaldo($saldo_anterior+$valor);
        $em->persist($movimiento_saldo);
        $em->flush();

    }
}
