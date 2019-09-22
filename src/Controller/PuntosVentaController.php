<?php

namespace App\Controller;

use App\Entity\PuntosVenta;
use App\Entity\Gasolineras;
use App\Entity\PuntosVentaUsuarios;
use App\Entity\FosUser;
use App\Form\PuntosVentaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/puntos/venta")
 */
class PuntosVentaController extends AbstractController
{
    /**
     * @Route("/", name="puntos_venta_index", methods={"GET"})
     */
    public function index(): Response
    {
        $puntosVentas = $this->getDoctrine()
            ->getRepository(PuntosVenta::class)
            ->findAll();

        return $this->render('puntos_venta/index.html.twig', [
            'puntos_ventas' => $puntosVentas,
        ]);
    }

    /**
     * @Route("/gasolinera/{id}", name="puntos_venta_list_gasolinera", methods={"GET"})
     */
    public function lista_por_gasolinera(Gasolineras $gasolinera , Request $request): Response
    {
        $puntosVentas = $this->getDoctrine()
            ->getRepository(PuntosVenta::class)
            ->findByGasolinera($gasolinera);

        return $this->render('puntos_venta/index.html.twig', [
            'puntos_ventas' => $puntosVentas,
        ]);
    }

    /**
     * @Route("/new", name="puntos_venta_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $puntosVentum = new PuntosVenta();
        $form = $this->createForm(PuntosVentaType::class, $puntosVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($puntosVentum);
            $entityManager->flush();

            return $this->redirectToRoute('puntos_venta_index');
        }

        return $this->render('puntos_venta/new.html.twig', [
            'puntos_ventum' => $puntosVentum,
            'form' => $form->createView(),
        ]);
    }

    /**
    *@Route("/usuarios/{id}/new", name="puntos_venta_usuarios_new", methods={"GET"})
    */
    public function usuarios_new(PuntosVenta $puntoventa): Response
    {
        $em=$this->getDoctrine()->getManager();
        $usuarios=$em->getRepository('App:PuntosVentaUsuarios')->SelectUsuariosPuntoVenta($puntoventa->getId());
       
         return $this->render('puntos_venta/nuevo_usuario.html.twig', [
            'punto_venta' => $puntoventa,
            'usuarios'=>$usuarios
        ]);
    }

    /**
     * @Route("/save/user", name="puntos_venta_usuarios_save", methods={"POST"})
     */
    public function usuarios_save(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $puntoventa_id=$request->get("punto_venta_id");
        $usuario_id=$request->get("usuario_id");
        $existe=$em->getRepository('App:PuntosVentaUsuarios','f')->findOneBy(array('puntoventaId'=>$puntoventa_id,'usuarioId'=>$usuario_id));

        $puntoventa=$em->getRepository('App:PuntosVenta','p')->find($puntoventa_id);
        if($existe){
            $puntoventa_usuarios=$existe;
        }else{
            $puntoventa_usuarios=new PuntosVentaUsuarios();
            $puntoventa_usuarios->setPuntoVenta($puntoventa);
        }
        $puntoventa_usuarios->setUsuarioId($usuario_id);
        $em->persist($puntoventa_usuarios);
        $em->flush();

        return $this->redirectToRoute('puntos_venta_usuarios',array('id'=>$puntoventa_id));

    }


    /**
     * @Route("/usuarios/{id}", name="puntos_venta_usuarios", methods={"GET"})
     */
    public function usuarios(PaginatorInterface $paginator, PuntosVenta $puntosVentum, Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        
        $qb=$em->getRepository('App:PuntosVentaUsuarios')->listaUsuariosPuntoVenta($puntosVentum->getId());

        
        if($request->get('query')!=""){
            
            $qb->andWhere('f.username LIKE :fuzzy_query OR f.email LIKE :fuzzy_query');
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }
        

        $qb->andWhere('u.puntoventaId=:puntoventaId')
            ->setParameter('puntoventaId',$puntosVentum->getId());
            

       
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
    


         return $this->render('puntos_venta/usuarios.html.twig', [
            'puntoventa' => $puntosVentum,
            'usuarios'=>$pagination,
            'pagination'=>$pagination,
            'query'=>$request->get('query','')
        ]);





        return $this->render('puntos_venta/show.html.twig', [
            'puntos_ventum' => $puntosVentum,
        ]);
    }

     /**
     * @Route("/usuarios/delete/{id}", name="puntos_venta_usuarios_delete", methods={"GET"})
     */
    public function usuarios_delete(FosUser $FosUser): Response
    {
        
        $em=$this->getDoctrine()->getManager();
        
        $punto_venta_usuario=$em->getRepository('App:PuntosVentaUsuarios')->findOneBy(array('usuarioId'=>$FosUser->getId()));    
        
        $punto_venta_id=$punto_venta_usuario->getPuntoVenta()->getId();

        if(is_object($punto_venta_usuario)){
            $em->remove($punto_venta_usuario);
            $em->flush();
        }
        
        return $this->redirectToRoute('puntos_venta_usuarios',array('id'=>$punto_venta_id));

    }

    /**
     * @Route("/{id}", name="puntos_venta_show", methods={"GET"})
     */
    public function show(PuntosVenta $puntosVentum): Response
    {
        return $this->render('puntos_venta/show.html.twig', [
            'puntos_ventum' => $puntosVentum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="puntos_venta_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PuntosVenta $puntosVentum): Response
    {
        $form = $this->createForm(PuntosVentaType::class, $puntosVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('puntos_venta_index');
        }

        return $this->render('puntos_venta/edit.html.twig', [
            'puntos_ventum' => $puntosVentum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="puntos_venta_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PuntosVenta $puntosVentum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$puntosVentum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($puntosVentum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('puntos_venta_index');
    }
}
