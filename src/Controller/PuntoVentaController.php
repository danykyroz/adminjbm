<?php

namespace App\Controller;

use App\Entity\PuntoVenta;
use App\Form\PuntoVentaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\HelperController;
/**
 * @Route("/admin/puntos/venta")
 */
class PuntoVentaController extends HelperController
{
    /**
     * @Route("/", name="punto_venta_index", methods={"GET"})
     */
    public function index(): Response
    {
        $puntoVentas = $this->getDoctrine()
            ->getRepository(PuntoVenta::class)
            ->findAll();

        return $this->render('punto_venta/index.html.twig', [
            'punto_ventas' => $puntoVentas,
        ]);
    }

    /**
     * @Route("/new", name="punto_venta_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $puntoVentum = new PuntoVenta();
        $form = $this->createForm(PuntoVentaType::class, $puntoVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $rol="ROLE_VENDEDOR";

            $user=$this->createUserClient($puntoVentum,$rol);    
            if(!$user){
                $this->addFlash('bad', 'Ya existe un cliente o usuario con este correo');


            }else{

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($puntoVentum);
                $entityManager->flush();

                $this->addFlash('success', 'Cliente creado exitosamente!');
                return $this->redirectToRoute('punto_venta_index');

                
            }
            
        }

        return $this->render('punto_venta/new.html.twig', [
            'punto_ventum' => $puntoVentum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="punto_venta_show", methods={"GET"})
     */
    public function show(PuntoVenta $puntoVentum): Response
    {
        return $this->render('punto_venta/show.html.twig', [
            'punto_ventum' => $puntoVentum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="punto_venta_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PuntoVenta $puntoVentum): Response
    {
        $form = $this->createForm(PuntoVentaType::class, $puntoVentum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('punto_venta_index');
        }

        return $this->render('punto_venta/edit.html.twig', [
            'punto_ventum' => $puntoVentum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="punto_venta_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PuntoVenta $puntoVentum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$puntoVentum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $user = $entityManager->getRepository("App:FosUser")->findOneBy(["email" => $puntoVentum->getEmail()]);

            if($user){
                $entityManager->remove($user);
            }

            $entityManager->remove($puntoVentum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('punto_venta_index');
    }

    protected function createNewPuntoVentaEntity()
    {
        $flotilla = new PuntoVenta();
        echo "aa";
        die();
        // ...

        return $flotilla;
    }
}
