<?php

namespace App\Controller;

use App\Entity\FosUser;
use App\Entity\Gasolineras;
use App\Entity\GasolineraUsuarios;

use App\Form\GasolinerasType;
use App\Repository\GasolinerasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/admin/gasolineras")
 */
class GasolinerasController extends AbstractController
{
    /**
     * @Route("/", name="gasolineras_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $qb = $this->getDoctrine()
            ->getRepository(Gasolineras::class)->orderById();

        if($request->get('query')!=""){
            //where f.id like '%query%'
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'g', 'id'));
            //or f.nombre like '%query%'
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'g', 'nombre'));
            //or f.nombre like '%query%'
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'g', 'nombreEncargado'));

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'g', 'telefonoEncargado'));

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'g', 'direccion'));

            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        return $this->render('gasolineras/index.html.twig', [
            'gasolineras' => $pagination,
            'pagination'=> $pagination,
            'query'=>$request->get('query',''),
        ]);
    }

     /**
     * @Route("/usuarios/{id}", name="gasolineras_usuarios", methods={"GET"})
     */
    public function usuarios(Gasolineras $gasolinera, PaginatorInterface $paginator, Request $request): Response
    {
        $em=$this->getDoctrine()->getManager();

        $qb=$em->getRepository(Gasolineras::class)->listaUsuariosGasolinera($gasolinera->getId());

        if($request->get('query')!=""){

            $qb->andwhere('f.username LIKE :fuzzy_query OR f.email LIKE :fuzzy_query');

            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }


        $qb->andWhere('u.gasolineraId=:gasolineraId')
            ->setParameter('gasolineraId',$gasolinera->getId());

        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        return $this->render('gasolineras/usuarios.html.twig', [
            'gasolinera' => $gasolinera,
            'usuarios'=>$pagination,
            'pagination'=>$pagination,
            'query'=>$request->get('query','')
        ]);
    }

    /**
     * @Route("/usuarios/{id}/new", name="gasolineras_usuarios_new", methods={"GET"})
     */
    public function usuarios_new(Gasolineras $gasolinera): Response
    {
        $em = $this->getDoctrine()->getManager();
        $usuarios = $em->getRepository(Gasolineras::class)->SelectUsuariosGasolinera($gasolinera->getId());

        return $this->render('gasolineras/nuevo_usuario.html.twig', [
            'gasolinera' => $gasolinera,
            'usuarios' => $usuarios
        ]);
    }

    /**
     * @Route("/save/user", name="gasolineras_usuarios_save", methods={"POST"})
     */
    public function usuarios_save(Request $request): Response
    {

        $em=$this->getDoctrine()->getManager();

        $gasolinera_id=$request->get("gasolinera_id");
        $usuario_id=$request->get("usuario_id");
        $existe=$em->getRepository('App:GasolineraUsuarios','f')->findOneBy(array('gasolineraId'=>$gasolinera_id,'usuarioId'=>$usuario_id));

        $gasolinera=$em->getRepository('App:Gasolineras','f')->find($gasolinera_id);
        if($existe){
            $gasolineras_usuarios=$existe;
        }else{
            $gasolineras_usuarios=new GasolineraUsuarios();
            $gasolineras_usuarios->setGasolineraId($gasolinera_id);
        }
        $gasolineras_usuarios->setUsuarioId($usuario_id);
        $em->persist($gasolineras_usuarios);
        $em->flush();

        return $this->redirectToRoute('gasolineras_usuarios',array('id'=>$gasolinera_id));

    }

    /**
     * @Route("/usuarios/delete/{id}", name="gasolineras_usuarios_delete", methods={"GET"})
     */
    public function usuarios_delete(FosUser $FosUser): Response
    {

        $em=$this->getDoctrine()->getManager();

        $gasolinera_usuario=$em->getRepository('App:GasolineraUsuarios')->findOneBy(array('usuarioId'=>$FosUser->getId()));

        $gasolinera_id=$gasolinera_usuario->getGasolineraId();
        if(is_object($gasolinera_usuario)){
            $em->remove($gasolinera_usuario);
            $em->flush();
        }

        return $this->redirectToRoute('gasolineras_usuarios',array('id'=>$gasolinera_id));

    }

    /**
     * @Route("/new", name="gasolineras_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $gasolinera = new Gasolineras();
        $form = $this->createForm(GasolinerasType::class, $gasolinera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($gasolinera);
            $entityManager->flush();

            $this->setGeoLocation($gasolinera);

            return $this->redirectToRoute('gasolineras_index');
        }

        return $this->render('gasolineras/new.html.twig', [
            'gasolinera' => $gasolinera,
            'form' => $form->createView(),
        ]);
    }


    private function setGeoLocation($gasolinera){

        $em = $this->getDoctrine()->getManager();

        $delegacion=$gasolinera->getDelegacion()->getMunicipio();
        $pais="Mexico";
        $address = $gasolinera->getDireccion().','.$delegacion.','.$pais;
        $url = "https://maps.google.com/maps/api/geocode/json?address=".urlencode($address).'&key=AIzaSyC5NO176eQLzjtHKdfew1EUFMY-pVqPCqU';

        $response=json_decode(file_get_contents($url));

        if ($response->status == 'OK') {

            $latitude = $response->results[0]->geometry->location->lat;
            $longitude = $response->results[0]->geometry->location->lng;
            $gasolinera->setLatitud($latitude);
            $gasolinera->setLongitud($longitude);
            $em->persist($gasolinera);
            $em->flush();

            
        } else {
           
        } 
        
    }

    /**
     * @Route("/{id}", name="gasolineras_show", methods={"GET"})
     */
    public function show(Gasolineras $gasolinera): Response
    {
        return $this->render('gasolineras/show.html.twig', [
            'gasolinera' => $gasolinera,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gasolineras_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Gasolineras $gasolinera): Response
    {
        $form = $this->createForm(GasolinerasType::class, $gasolinera);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->setGeoLocation($gasolinera);
            //return $this->redirectToRoute('gasolineras_index');
        }

        return $this->render('gasolineras/edit.html.twig', [
            'gasolinera' => $gasolinera,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gasolineras_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Gasolineras $gasolinera): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gasolinera->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($gasolinera);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gasolineras_index');
    }


}
