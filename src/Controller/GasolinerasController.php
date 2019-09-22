<?php

namespace App\Controller;

use App\Entity\Gasolineras;
use App\Form\GasolinerasType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/gasolineras")
 */
class GasolinerasController extends AbstractController
{
    /**
     * @Route("/", name="gasolineras_index", methods={"GET"})
     */
    public function index(): Response
    {
        $gasolineras = $this->getDoctrine()
            ->getRepository(Gasolineras::class)
            ->findAll();

        return $this->render('gasolineras/index.html.twig', [
            'gasolineras' => $gasolineras,
        ]);
    }

     /**
     * @Route("/", name="gasolineras_usuarios", methods={"GET"})
     */
    public function usuarios(): Response
    {
        $gasolineras = $this->getDoctrine()
            ->getRepository(Gasolineras::class)
            ->findAll();

        return $this->render('gasolineras/usuarios.html.twig', [
            'gasolineras' => $gasolineras,
        ]);
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

            return $this->redirectToRoute('gasolineras_index');
        }

        return $this->render('gasolineras/new.html.twig', [
            'gasolinera' => $gasolinera,
            'form' => $form->createView(),
        ]);
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

            return $this->redirectToRoute('gasolineras_index');
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
