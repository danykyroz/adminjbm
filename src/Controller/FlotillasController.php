<?php

namespace App\Controller;

use App\Entity\Flotillas;
use App\Entity\FlotillaUsuarios;
use App\Entity\FosUser;
use App\Entity\Clientes;
use App\Entity\Wallet;


use App\Form\FlotillasType;
use App\Form\ClientesType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\HelperController;
use App\Entity\FlotillasClientes;

/**
 * @Route("/admin/flotillas")
 */
class FlotillasController extends HelperController
{
    /**
     * @Route("/", name="flotillas_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        
        $qb=$em->createQueryBuilder();
        $qb->select('f')->from('App:Flotillas','f')
        ->GroupBy('f.id');

        

        if($request->get('query')!=""){
            //where f.id like '%query%'
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'f', 'id'));
            //or f.nombre like '%query%'

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'f', 'nombre'));
            //or f.nombre like '%query%'
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'f', 'documento'));
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }   

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        $lista_flotillas=array();
        foreach ($pagination as $key => $flotilla) {
             $lista_flotillas[]=array('row'=>$flotilla,'saldo'=>$this->getSaldoFlotilla($flotilla->getId()));
        }

        return $this->render('flotillas/index.html.twig', [
            'flotillas' => $pagination,
            'pagination'=> $pagination,
            'lista_flotillas'=>$lista_flotillas,
            'query'=>$request->get('query',''),
        ]);
    }

    private function getSaldoFlotilla($id){

        $em=$this->getDoctrine()->getManager();
        
        $qb=$em->createQueryBuilder();
        $qb->select('sum(w.saldo) as saldo')->from('App:Flotillas','f')
        ->innerJoin('App:FlotillasClientes', 'fc', 'WITH', 'fc.flotillaId = f')
        ->innerJoin('App:Clientes', 'c', 'WITH', 'fc.clienteId = c')
        ->innerJoin('App:Wallet', 'w', 'WITH', 'w.clienteId = c')
        ->where('f.id=:id')->setParameter('id',$id);
        return $qb->getQuery()->getSingleScalarResult();
        
    }

    /**
     * @Route("/usuarios/{id}", name="flotillas_usuarios", methods={"GET"})
     */
    public function usuarios(Flotillas $flotilla, PaginatorInterface $paginator, Request $request): Response
    {
        $em=$this->getDoctrine()->getManager();
        
        $qb=$em->getRepository('App:FlotillaUsuarios')->listaUsuariosFlotilla($flotilla->getId());

        
        if($request->get('query')!=""){
            
            $qb->andwhere('f.username LIKE :fuzzy_query OR f.email LIKE :fuzzy_query');
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }
        

        $qb->andWhere('u.flotillaId=:flotillaId')
            ->setParameter('flotillaId',$flotilla->getId());
            

       
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );


        $lista_usuarios=array();
        foreach ($pagination as $key => $usuario) {
             $lista_usuarios[]=array('row'=>$usuario,'saldo'=>$this->getClienteUsuarioFlotilla($usuario));
        }

       
         return $this->render('flotillas/usuarios.html.twig', [
            'flotilla' => $flotilla,
            'usuarios'=>$pagination,
            'lista_usuarios'=>$lista_usuarios,
            'pagination'=>$pagination,
            'query'=>$request->get('query','')
        ]);
    }

    private function getClienteUsuarioFlotilla($user_admin){
        
        $em=$this->getDoctrine()->getManager();
        $saldo=0;

        $flotilla_cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user_admin->getEmail()));

        if($flotilla_cliente){
            
            $wallet_flotilla=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$flotilla_cliente->getId()));

            if($wallet_flotilla){
                $saldo=$wallet_flotilla->getSaldo();
            }
        }
        return $saldo;
        
    }

    /**
     * @Route("/usuarios/{id}/new", name="flotillas_usuarios_new", methods={"GET"})
     */
    public function usuarios_new(Flotillas $flotilla): Response
    {
        $em=$this->getDoctrine()->getManager();
        $usuarios=$em->getRepository('App:FlotillaUsuarios')->SelectUsuariosFlotilla($flotilla->getId());
       
         return $this->render('flotillas/nuevo_usuario.html.twig', [
            'flotilla' => $flotilla,
            'usuarios'=>$usuarios
        ]);
    }

     /**
     * @Route("/save/user", name="flotillas_usuarios_save", methods={"POST"})
     */
    public function usuarios_save(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $flotilla_id=$request->get("flotilla_id");
        $usuario_id=$request->get("usuario_id");
        $existe=$em->getRepository('App:FlotillaUsuarios','f')->findOneBy(array('flotillaId'=>$flotilla_id,'usuarioId'=>$usuario_id));

        $flotilla=$em->getRepository('App:Flotillas','f')->find($flotilla_id);
        if($existe){
            $flotillas_usuarios=$existe;
        }else{
            $flotillas_usuarios=new FlotillaUsuarios();
            $flotillas_usuarios->setFlotilla($flotilla);
        }
        $flotillas_usuarios->setUsuarioId($usuario_id);
        $em->persist($flotillas_usuarios);
        $em->flush();

        return $this->redirectToRoute('flotillas_usuarios',array('id'=>$flotilla_id));

    }

     /**
     * @Route("/usuarios/delete/{id}", name="flotillas_usuarios_delete", methods={"GET"})
     */
    public function usuarios_delete(FosUser $FosUser): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        
        $flotilla_usuario=$em->getRepository('App:FlotillaUsuarios')->findOneBy(array('usuarioId'=>$FosUser->getId()));    
        
        $flotilla_id=$flotilla_usuario->getFlotillaId();
        if(is_object($flotilla_usuario)){
            $em->remove($flotilla_usuario);
            $em->flush();
        }
        
        return $this->redirectToRoute('flotillas_usuarios',array('id'=>$flotilla_id));

    }



    /**
     * @Route("/new", name="flotillas_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $flotilla = new Flotillas();
        $form = $this->createForm(FlotillasType::class, $flotilla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($flotilla);
            $entityManager->flush();

            return $this->redirectToRoute('flotillas_index');
        }

        return $this->render('flotillas/new.html.twig', [
            'flotilla' => $flotilla,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="flotillas_show", methods={"GET"})
     */
    public function show(Flotillas $flotilla): Response
    {
        return $this->render('flotillas/show.html.twig', [
            'flotilla' => $flotilla,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="flotillas_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Flotillas $flotilla): Response
    {
        $form = $this->createForm(FlotillasType::class, $flotilla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('flotillas_index');
        }

        return $this->render('flotillas/edit.html.twig', [
            'flotilla' => $flotilla,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="flotillas_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Flotillas $flotilla): Response
    {
        if ($this->isCsrfTokenValid('delete'.$flotilla->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($flotilla);
            $entityManager->flush();
        }

        return $this->redirectToRoute('flotillas_index');
    }

    /**
     * @Route("/clientes/{id}", name="flotillas_clientes", methods={"GET"})
     */
    public function clientes(Flotillas $flotilla, PaginatorInterface $paginator, Request $request): Response
    {
        $em=$this->getDoctrine()->getManager();

        $qb=$em->getRepository('App:FlotillasClientes')->listaClientesFlotilla($flotilla->getId());


        if($request->get('query')!=""){

            $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));

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

        $qb->andWhere('fc.flotillaId=:flotillaId')
            ->setParameter('flotillaId',$flotilla->getId());

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        $lista_clientes=array();

        foreach ($pagination as $key=>$cliente){
                
                $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

                $lista_clientes[]=array('row'=>$cliente,'saldo'=>$wallet_cliente->getSaldo());
        }
         
        return $this->render('flotillas/clientes.html.twig', [
            'flotilla' => $flotilla,
            'clientes'=>$pagination,
            'lista_clientes'=>$lista_clientes,
            'pagination'=>$pagination,
            'query'=>$request->get('query',''),
            'tipo'=>array('1'=>'Cliente Wallet','2'=>'Admin Flotilla'),
            'query'=>$request->get('query','')

        ]);
    }

    /**
     * @Route("/clientes/new/{id}", name="flotillas_clientes_new", methods={"GET","POST"})
     */
    public function clientes_new(Flotillas $flotilla, Request $request): Response
    {
        $cliente = new Clientes();
        $form = $this->createForm(ClientesType::class, $cliente);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $em=$this->getDoctrine()->getManager();

       
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


                if($flotilla){
                  //Buscarmos dentro de lo user flotilla el id  
                   
                    if($flotilla){
                        $flotilla_id=$flotilla->getId();
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
                
                return $this->redirectToRoute('flotillas_clientes',array('id'=>$flotilla->getId()));
            }
      
        }

        return $this->render('flotillas/clientes_new.html.twig', [
            'cliente' => $cliente,
            'form' => $form->createView(),
            'flotilla'=>$flotilla
        ]);
    }
}
