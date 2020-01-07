<?php

namespace App\Controller;


use App\Entity\CuentasPorCobrar;
use App\Entity\Empleados;
use App\Entity\Pagos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{


    /**
     * @Route("/", name="index")
     */

    public function index(Request $request)
    {
        // en index pagina con datos generales de la app
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $user = ($this->getUser());

        if (!$user) {
            return $this->redirect('login');
        } else {

            $tcomprobantes = $em->getRepository(Pagos::class)->findBy([]);
            $ccRepo = $em->getRepository(CuentasPorCobrar::class);
            $templeados = $em->getRepository(Empleados::class)->findAll();

            $params = [
                'comprobantesReportados' => count($tcomprobantes),
                'cuentasPorCobrar' => count($ccRepo->findCuentasPorCobrar()),
                'empleados' => count($templeados)
            ];

            if ($user->getRoles()[0] == "ROLE_ADMIN") {
                return $this->render('index.html.twig', $params);
            }
            if ($user->getRoles()[0] == "ROLE_AUXILIAR") {
                return $this->render('index.html.twig', $params);
            }
            if ($user->getRoles()[0] == "ROLE_CLIENTE") {
                return $this->render('index.html.twig', $params);
            }

        }


    }


    /**
     * @Route("/menu", name="default_menu")
     */
    public function menu(Request $request)
    {

        $user = ($this->getUser());

        $em = $this->getDoctrine()->getManager();

        if (!$user) {
            return $this->redirect('login');
        } else {

            if ($user->getRoles()[0] == "ROLE_ADMIN") {
                return $this->render('home/menu_admin.html.twig');
            }
            if ($user->getRoles()[0] == "ROLE_AUXILIAR") {
                return $this->render('home/menu_auxiliar.html.twig');
            }
            if ($user->getRoles()[0] == "ROLE_CLIENTE") {
                return $this->render('home/menu_cliente.html.twig');
            }

        }

    }


    /**
     * @Route("/", name="landing")
     */

    public function landing(Request $request)
    {
        // en index pagina con datos generales de la app
        return $this->render('index.html.twig');
    }


}


