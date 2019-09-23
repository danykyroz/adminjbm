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
 * @Route("/admin/emails")
*/

class EmailsController extends AbstractController
{


/**
* @Route("/index", name="email_index", methods={"GET"})
*/
public function index(\Swift_Mailer $mailer, Request $request)
{
    
    $link="http://permergas.app";
    $asunto="Reseteo de contraseña";

    $body=$this->renderView(
                // templates/emails/registration.html.twig
                'emails/reset_password.html.twig',
                ['link' => $link,'asunto'=>$asunto]
            );



    $message = (new \Swift_Message('Hello Email'))
        ->setFrom('danykyroz@gmail.com')
        ->setTo('danykyroz@gmail.com')
        ->setBody(
           $body,'text/html'
        );
    
    
    $mailer->send($message);

    return new Response($body);
}

}