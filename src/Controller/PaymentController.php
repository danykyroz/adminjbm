<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use \Conekta\Order as ConektaOrder;

class PaymentController extends Controller
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

	private function getOrder($valor){

		\Conekta\Conekta::setApiKey("key_fp96gh3qzdWuBrVTFAxErA");
  		 $user=($this->getUser());
         $valor=intval($valor.'00');
        
  		$order =
    array(
           'line_items'=> array(
            array(
                'name'        => 'Box of Cohiba S1s',
                'description' => 'Imported From Mex.',
                'unit_price'  => $valor,
                'quantity'    => 1,
                'sku'         => 'cohb_s1',
                'category'    => 'food',
                'tags'        => array('food', 'mexican food')
                )
           ),
          'currency'    => 'mxn',
          'metadata'    => array('test' => 'extra info'),
          'charges'     => array(
              array(
                  'payment_method' => array(
                      'type'       => 'oxxo_cash',
                      'expires_at' => strtotime(date("Y-m-d H:i:s")) + "36000"
                   ),
                   'amount' => $valor
                )
            ),
            'currency'      => 'mxn',
            'customer_info' => array(
                'name'  => $user->getUsername(),
                'phone' => '+5213353319758',
                'email' => $user->getEmail()
            )
        );

    return $order;

	}
 }