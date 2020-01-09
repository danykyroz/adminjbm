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
                $facturadoMensual = $this->getFacturadoMensual($tcomprobantes);
                $estadosFacturas = $this->getEstadosFacturas($tcomprobantes);

                $params = [
                    'comprobantesReportados' => count($tcomprobantes),
                    'cuentasPorCobrar' => count($ccRepo->findCuentasPorCobrar()),
                    'empleados' => count($templeados),
                    'facturadoMensual' => $facturadoMensual,
                    'estadosFacturas' => $estadosFacturas,
                ];


             if ($user->getRoles()[0] == "ROLE_CLIENTE") {
               

                return $this->render('index.html.twig', $params);
            }

            $tclientes=$em->getRepository('App:Clientes')->findAll();
            $tauxiliares=$em->getRepository('App:Auxiliares')->findAll();
            $tarchivos=$em->getRepository('App:CuentasPorCobrar')->findAll();
            
            $clientes=count($tclientes);
            $auxiliares=count($tauxiliares);
            $archivos=count($tarchivos);
            
            $params['clientes']=$clientes;
            $params['auxiliares']=$auxiliares;
            $params['archivos']=$archivos;


            if ($user->getRoles()[0] == "ROLE_ADMIN") {
                return $this->render('index_admin.html.twig', $params);
            }
            if ($user->getRoles()[0] == "ROLE_AUXILIAR") {

                return $this->render('index_admin.html.twig', $params);
            }
        

        }


    }

    public function getFacturadoMensual($pagos) {

        $facturado = [];
        foreach ($pagos as $pago){
            $mes = $pago->getFecha()->format('m');

            if(!isset($facturado[Pagos::getMes($mes)] )) {
                $facturado[Pagos::getMes($mes)]  = $pago->getFacturado();
            } else {
                $facturado[Pagos::getMes($mes)] += $pago->getFacturado();
            }

        }

        if(count($facturado) > 0) {
            return [
                'labels' => array_keys($facturado),
                'values' => array_values($facturado)
            ];

        }

        return null;

    }

    public function getEstadosFacturas($pagos) {

        $estados = [
            'facturado' => 0,
            'porfacturar' => 0
        ];
        foreach ($pagos as $pago){
            $estados['facturado'] += $pago->getFacturado();
            $estados['porfacturar'] += $pago->getPorFacturar();
        }

        return $estados;

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


