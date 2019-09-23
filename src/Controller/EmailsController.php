<?php

namespace App\Controller;

use App\Entity\Clientes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\UserProviderInterface;
	
/**
 * @Route("/emails")
*/

class EmailsController extends AbstractController
{

//public $base_link="http://permergas.app";
public $base_link="http://167.71.157.84";

/**
* @Route("/index", name="email_index", methods={"GET"})
*/
public function index(\Swift_Mailer $mailer, Request $request)
{
    


    /*$transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, "ssl");
    $transport->setUsername('permergas.app@gmail.com');
    $transport->setPassword('PG1321gmail');

    $mailer = new \Swift_Mailer($transport);
    */


    $message = (new \Swift_Message($asunto))
        ->setFrom('permergas.app@gmail.com','Permergas App')
        ->setTo('cero1studio@gmail.com')
        ->addTo('danykyroz@gmail.com')
        ->setBody(
           $body,'text/html'
        );
    
    
    $mailer->send($message);

    return new Response($body);
}


/**
* @Route("/recuperar/cuenta", name="email_recuperar_cuenta", methods={"GET"})
*/
public function email_recuperar_cuenta_(\Swift_Mailer $mailer, Request $request)
{
    
    $em=$this->getDoctrine()->getManager();
    
    $host=$request->getschemeAndHttpHost();

    $asunto="Recuperar contraseña Permergas";
    $email=$request->get('email',''); 
    $fosuser=$em->getRepository('App:FosUser','f')->findOneByEmail($email);
    $token=$fosuser->getConfirmationToken();
    $link=$host."/reset/cuenta/{$token}";

    $body=$this->renderView(
                // templates/emails/registration.html.twig
                'emails/reset_password.html.twig',
                ['link' => $link,'asunto'=>$asunto,'username'=>$fosuser->getUsername()]
            );

    /*$transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, "ssl");
    $transport->setUsername('permergas.app@gmail.com');
    $transport->setPassword('PG1321gmail');

    $mailer = new \Swift_Mailer($transport);
    */


    $message = (new \Swift_Message($asunto))
        ->setFrom('permergas.app@gmail.com','Permergas App')
        ->setTo($email,$fosuser->getUsername())
        ->addTo('danykyroz@gmail.com')
        ->setBody(
           $body,'text/html'
        );
    
    
    $mailer->send($message);

    return new Response('email enviado a:'.$link);
}






}