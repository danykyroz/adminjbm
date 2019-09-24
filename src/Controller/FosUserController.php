<?php

namespace App\Controller;

use App\Entity\FosUser;
use App\Form\FosUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Controller\HelperController;

/**
 * @Route("/admin/usuarios")
 */
class FosUserController extends HelperController
{
    /**
     * @Route("/", name="usuarios_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $qb = $this->getDoctrine()
            ->getRepository(FosUser::class)->ListarUsuarios();
        
        $qb->orWhere('f.roles LIKE :role_admin');
        $qb->setParameter('role_admin',"%ROLE_ADMIN%");
        $qb->andWhere('f.roles NOT LIKE :role_flotilla');
        $qb->setParameter('role_flotilla',"%ROLE_ADMIN_FLOTILLA%");
        
        if($request->get('query')!=""){

            $qb->andWhere('(f.email LIKE :fuzzy_query OR f.username LIKE :fuzzy_query)');

            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }    
        
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );

        return $this->render('usuarios/index.html.twig', [
            'usuarios' => $pagination,
            'query'=>$request->get('query','')

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
        $em=$this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $role = $form->get('roles')->getData();
           
            
            $fosUser->setRoles(array($role));
            
            $em->persist($fosUser);
            $em->flush();
                
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
