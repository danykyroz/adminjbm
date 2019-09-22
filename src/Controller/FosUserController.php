<?php

namespace App\Controller;

use App\Entity\FosUser;
use App\Form\FosUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/usuarios")
 */
class FosUserController extends AbstractController
{
    /**
     * @Route("/", name="usuarios_index", methods={"GET"})
     */
    public function index(): Response
    {
        $fosUsers = $this->getDoctrine()
            ->getRepository(FosUser::class)
            ->findAll();

        return $this->render('usuarios/index.html.twig', [
            'usuarios' => $fosUsers,
        ]);
    }

    /**
     * @Route("/new", name="usuarios_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fosUser = new FosUser();
        $form = $this->createForm(FosUserType::class, $fosUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fosUser);
            $entityManager->flush();

            return $this->redirectToRoute('usuarios_index');
        }

        return $this->render('usuarios/new.html.twig', [
            'fos_user' => $fosUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuarios_show", methods={"GET"})
     */
    public function show(FosUser $fosUser): Response
    {
        return $this->render('usuarios/show.html.twig', [
            'fos_user' => $fosUser,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="usuarios_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FosUser $fosUser): Response
    {
        $form = $this->createForm(FosUserType::class, $fosUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('usuarios_index');
        }

        return $this->render('usuarios/edit.html.twig', [
            'fos_user' => $fosUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="usuarios_delete", methods={"DELETE"})
     */
    public function delete(Request $request, FosUser $fosUser): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fosUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fosUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('usuarios_index');
    }
}
