<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use \Conekta\Order as ConektaOrder;

class PaymentController extends ClientesController
{


  /**
   * @Route("/admin/payment", name="admin_payment",methods={"GET","POST"})
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
   * @Route("/payment/credito", name="payment_credito",methods={"POST"})
   */
  public function payment_credito(Request $request){

    $token_id=$request->get("conektaTokenId");
    $valor=$request->get('valor');
    $type="card";
    $em=$this->getDoctrine()->getManager();
    $user=($this->getUser());
     
    $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
      
    $order=$this->getOrder($valor,$cliente,'tok_test_visa_4242',$type);

    try {
          $orden = ConektaOrder::create($order);
          $data=array("orden"=>$orden);
          
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
   * @Route("/payment/descontar/qr", name="payment_descontar_qr_credito",methods={"POST","GET"})
   */
  public function payment_descontar_qr(Request $request){

    $em=$this->getDoctrine()->getManager();

     $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$request->get('email_user')));
    
     
      $wallet_cliente=$em->getRepository('App:Wallet','w')->findOneBy(array('clienteId'=>$cliente->getId()));

        $saldo_anterior=$wallet_cliente->getSaldo();
        $operacion="resta";
        
        $valor=$request->get('valor');

        $this->crearTransaccion($saldo_anterior,$valor,$operacion,$wallet_cliente,$request->getClientIp());


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
 }