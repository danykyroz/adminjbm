<?php

namespace App\Controller;

use App\Entity\Flotillas;
use App\Entity\FlotillaUsuarios;
use App\Entity\FosUser;


use App\Form\FlotillasType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/admin/flotillas")
 */
class FlotillasController extends AbstractController
{
    /**
     * @Route("/", name="flotillas_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        //Conectarme a la bd
        $em=$this->getDoctrine()->getManager();
        //Inicializar una consulta
        $qb=$em->createQueryBuilder();
        //Select from flotillas 
        $qb->select('f')->from('App:Flotillas','f');
        

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
    
        return $this->render('flotillas/index.html.twig', [
            'flotillas' => $pagination,
            'pagination'=> $pagination,
            'query'=>$request->get('query',''),
        ]);
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
    


         return $this->render('flotillas/usuarios.html.twig', [
            'flotilla' => $flotilla,
            'usuarios'=>$pagination,
            'pagination'=>$pagination,
            'query'=>$request->get('query','')
        ]);
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



        return $this->render('flotillas/clientes.html.twig', [
            'flotilla' => $flotilla,
            'clientes'=>$pagination,
            'pagination'=>$pagination,
            'query'=>$request->get('query',''),
            'tipo'=>array('1'=>'Cliente Wallet','2'=>'Admin Flotilla'),
            'query'=>$request->get('query','')

        ]);
    }
}
