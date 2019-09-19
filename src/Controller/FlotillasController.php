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
        $flotillas = $this->getDoctrine()
            ->getRepository(Flotillas::class)
            ->findAll();
        
        $em=$this->getDoctrine()->getManager();
        
        $qb=$em->createQueryBuilder();

        $qb->select('f')->from('App:Flotillas','f');
        

        if($request->get('query')!=""){

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'f', 'id'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'f', 'nombre'));
            
            
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
    public function usuarios(Flotillas $flotilla): Response
    {
        $em=$this->getDoctrine()->getManager();
        $usuarios=$em->getRepository('App:FlotillaUsuarios')->listaUsuariosFlotilla($flotilla->getId());

         return $this->render('flotillas/usuarios.html.twig', [
            'flotilla' => $flotilla,
            'usuarios'=>$usuarios
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

        if($existe){
            $flotillas_usuarios=$existe;
        }else{
            $flotillas_usuarios=new FlotillaUsuarios();
            $flotillas_usuarios->setFlotillaId($flotilla_id);
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
}
