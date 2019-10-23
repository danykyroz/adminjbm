<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use \Conekta\Order as ConektaOrder;
use App\Entity\Transacciones AS Transaccion;
use App\Entity\Movimientos AS Movimiento;
use App\Entity\MovimientoSaldos;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller
{


  /**
   * @Route("/", name="admin_payment",methods={"GET","POST"})
   */

  public function payment_index(Request $request)
  {
  			
  		if($request->isMethod('GET')){

  			return $this->render('pagos/index.html.twig');

  		}else{

  			$valor=$request->get('valor',200);
  			$order=$this->getOrder($valor);
  			try {
				  $orden = ConektaOrder::create($order);
				  $data=array("orden"=>$orden);
				  return $this->render('pagos/respuesta.html.twig',$data);
				} catch (\Conekta\ProcessingError $e){
				  echo $e->getMessage();
				} catch (\Conekta\ParameterValidationError $e){
				  echo $e->getMessage();
				} 

  		}
  		

  
	}


  /**
   * @Route("/conekta", name="payment_conekta",methods={"POST"})
   */
  public function payment_conekta(Request $request){

    $token_id=$request->get("token_id");
    $valor=$request->get('valor');
    $type=$request->get('medio_pago');
    $em=$this->getDoctrine()->getManager();
    $user=($this->getUser());
     
    $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
      
    $order=$this->getOrder($valor,$cliente,$token_id,$type);

    try {
          $orden = ConektaOrder::create($order);
          $data=array("orden"=>$orden);
          $data['type']=$type;
          if($type=='oxxo_cash'){
            $data["reference"]= $orden->charges[0]->payment_method->reference;
          
          }
          $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

        $saldo_anterior=$wallet_cliente->getSaldo();
        $operacion="suma";
        
        $this->crearTransaccion($saldo_anterior,$valor,$operacion,$wallet_cliente,$request->getClientIp());

         $wallet_cliente->setSaldo($wallet_cliente->getSaldo()+$valor);
         
         $em->persist($wallet_cliente);
         $em->flush();


          return $this->render('app/respuesta.html.twig',$data);
        } catch (\Conekta\ProcessingError $e){
          echo $e->getMessage();
        } catch (\Conekta\ParameterValidationError $e){
          echo $e->getMessage();
        } 

        die();
  
  }


   /**
   * @Route("/confirmar/qr", name="payment_confirmar_qr",methods={"POST","GET"})
   */
  public function payment_confirmar_qr(Request $request){

    $em=$this->getDoctrine()->getManager();
    $qr=$request->get('qr');
    $qr='MTMzMzMzMTU2ODU2Njk5MQ==';

    $wallet_cliente=$em->getRepository('App:Wallet','c')->findOneBy(array('qr'=>$qr));
    if($wallet_cliente){
       $valor=$request->get('valor');
     }else{
       return  $this->redirect($this->generateUrl('dashboard_vendedor'));
     }
   

    return $this->render('app_vendedor/confirmar_compra.html.twig',['wallet'=>$wallet_cliente,'valor'=>$valor]); 
  

  }




   /**
   * @Route("/descontar/qr", name="payment_descontar_qr_credito",methods={"POST","GET"})
   */
  public function payment_descontar_qr(Request $request){

    $em=$this->getDoctrine()->getManager();

    $em=$this->getDoctrine()->getManager();
    $qr=$request->get('qr');
    $qr='MTMzMzMzMTU2ODU2Njk5MQ==';

    $wallet_cliente=$em->getRepository('App:Wallet','c')->findOneBy(array('qr'=>$qr));
   
        $saldo_anterior=$wallet_cliente->getSaldo();
        $operacion="resta";
        
        $valor=$request->get('valor');
        $punto_venta_id=$request->get('punto_venta_id');

        $gasolinera_user=$em->getRepository('App:GasolineraUsuarios','u')->findOneBy(array('usuarioId'=>$this->getUser()->getId()));

        $gasolinera=$gasolinera_user->getGasolinera();


        $this->crearTransaccion($saldo_anterior,$valor,$operacion,$wallet_cliente,$request->getClientIp(),$punto_venta_id,$gasolinera);


        $wallet_cliente->setSaldo($wallet_cliente->getSaldo()-$valor);
         
         $em->persist($wallet_cliente);
         $em->flush();

        return  $this->redirect($this->generateUrl('dashboard_vendedor'));

  }

	private function getOrder($valor,$cliente,$token_id,$type){

		\Conekta\Conekta::setApiKey("key_fp96gh3qzdWuBrVTFAxErA");
  		 $user=($this->getUser());
         $valor=intval($valor.'00');
      
       


  	 	$order =
    array(
           'line_items'=> array(
            array(
                'name'        => 'Recarga',
                'description' => 'Recarga wallet permergas',
                'unit_price'  => $valor,
                'quantity'    => 1,
                'category'    => 'servicios',
                'tags'        => array('recarga', 'waller '.$user->getUsername())
                )
           ),
          'currency'    => 'mxn',
          'charges'     => array(
              array(
                  'payment_method' => array(
                      'type'       => $type,
                      'token_id'=>$token_id
                   ),
                   'amount' => $valor
                )
            ),
            'currency'      => 'mxn',
            'customer_info' => array(
                'name'  => $cliente->getNombres().' '.$cliente->getApellidos(),
                'phone' => '+52'.$cliente->getCelular(),
                'email' => $user->getEmail()
            )
        );

    return $order;

	}

   public function crearTransaccion($saldo_anterior,$valor,$operacion,$wallet,$ip,$punto_venta_id,$gasolinera){
        

        $em = $this->getDoctrine()->getManager();
        $tipo_movimiento=1;
        $nombre_gasolinera=$gasolinera->getNombre();

        $fos_user=$em->getRepository('App:FosUser','f')->find($this->getUser()->getId());
        if($operacion=='resta'){
            $tipo_movimiento=2;
        }
        

        $obj_tipo_movimiento=$em->getRepository('App:TipoMovimientos','t')->find($tipo_movimiento);

        if($operacion=="suma"){

            $tr=new Transaccion();
            $tr->setCreatedAt(new \DateTime('now'));
            $tr->setUpdatedAt(new \DateTime('now'));
            $tr->setWallet($wallet);
            $tr->setValor($valor);
            $tr->setGasolineraId($gasolinera->getId());
            $tr->setTipoTransaccion(1);
            $tr->setUsuarioId($this->getUser()->getId());
            $tr->setEstado('Aceptada');
            $tr->setRespuesta('Aprobada');
            $tr->setCodRespuesta(00);
            $tr->setIp($ip);
            $tr->setDispositivo('admin');

            $em->persist($tr);
            $em->flush();

        }


        if($operacion=="resta"){

            $tr=new Transaccion();
            $tr->setCreatedAt(new \DateTime('now'));
            $tr->setUpdatedAt(new \DateTime('now'));
            $tr->setFechaPago(new \DateTime('now'));
            $tr->setWallet($wallet);
            $tr->setValor($valor);
            $tr->setTipoTransaccion(2);
            $tr->setGasolineraId($gasolinera->getId());
            $tr->setPuntoVentaId($punto_venta_id);
            $tr->setNotas("Carga de Gasolina ($nombre_gasolinera) ");
            $tr->setUsuarioId($this->getUser()->getId());
            $tr->setEstado('Aceptada');
            $tr->setRespuesta('Aprobada');
            $tr->setCodRespuesta(00);
            $tr->setIp($ip);
            $tr->setDispositivo('admin');
            $em->persist($tr);
            $em->flush();

        }
        

        $movimiento=new Movimiento();
        $movimiento->setWallet($wallet);
        $movimiento->setCreatedAt(new \DateTime('now'));
        $movimiento->setUpdatedAt(new \DateTime('now'));
        $movimiento->setValor($valor);
        $movimiento->setTipoMovimiento($obj_tipo_movimiento);
        $movimiento->setSincronizado(1);
        $movimiento->setFosUser($fos_user);
        $em->persist($movimiento);
        $em->flush();

        $movimiento_saldo=new MovimientoSaldos();
        $movimiento_saldo->setCreatedAt(new \DateTime('now'));
        $movimiento_saldo->setUpdatedAt(new \DateTime('now'));
        $movimiento_saldo->setMovimiento($movimiento);
        $movimiento_saldo->setSaldoAnterior($saldo_anterior);
        $movimiento_saldo->setValor($valor);
        if($tipo_movimiento==1){
          $movimiento_saldo->setNuevoSaldo($saldo_anterior+$valor);
        }else{
           $movimiento_saldo->setNuevoSaldo($saldo_anterior-$valor);
        }
        $em->persist($movimiento_saldo);
        $em->flush();

    }
 }