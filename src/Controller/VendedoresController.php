<?php

namespace App\Controller;

use App\Entity\Vendedores;
use App\Form\VendedoresType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\HelperController;
/**
 * @Route("/admin/vendedores")
 */
class VendedoresController extends HelperController
{
    /**
     * @Route("/", name="vendedor_index", methods={"GET"})
     */
    public function index(): Response
    {
        $vendedores = $this->getDoctrine()
            ->getRepository(Vendedores::class)
            ->findAll();

        return $this->render('vendedores/index.html.twig', [
            'vendedores' => $vendedores,
        ]);
    }

    /**
     * @Route("/new", name="vendedor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $Vendedores = new Vendedores();
        $form = $this->createForm(VendedoresType::class, $Vendedores);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $rol="ROLE_VENTAS";

            $user=$this->createUserClient($Vendedores,$rol);    
            if(!$user){
                $this->addFlash('bad', 'Ya existe un cliente o usuario con este correo');


            }else{

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($Vendedores);
                $entityManager->flush();

                $this->addFlash('success', 'Cliente creado exitosamente!');
                return $this->redirectToRoute('vendedores_index');

                
            }
            
        }

        return $this->render('vendedores/new.html.twig', [
            'punto_ventum' => $Vendedores,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendedor_show", methods={"GET"})
     */
    public function show(Vendedores $vendedores): Response
    {
        return $this->render('vendedores/show.html.twig', [
            'punto_ventum' => $vendedores,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="vendedor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Vendedores $vendedores): Response
    {
        $form = $this->createForm(VendedoresType::class, $vendedores);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vendedores_index');
        }

        return $this->render('vendedores/edit.html.twig', [
            'punto_ventum' => $vendedores,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="vendedor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Vendedores $vendedores): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vendedores->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $user = $entityManager->getRepository("App:FosUser")->findOneBy(["email" => $Vendedores->getEmail()]);

            if($user){
                $entityManager->remove($user);
            }

            $entityManager->remove($vendedores);
            $entityManager->flush();
        }

        return $this->redirectToRoute('vendedores_index');
    }

    
}
