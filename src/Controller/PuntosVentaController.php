<?php

namespace App\Controller;

use App\Entity\PuntosVenta;
use App\Form\PuntosVentaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
