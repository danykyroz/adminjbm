<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/archivos")
 */

class ArchivosController extends AbstractController
{


  /**
   * @Route("/", name="index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
  	   	  $user=($this->getUser());
          $cliente=false;
          $em=$this->getDoctrine()->getManager();
          if($user->getRoles()[0]=="ROLE_CLIENTE"){
            $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
            $razon_social=$cliente->getRazonSocial();
          }

          $clienteid=$request->get('clienteid',0);
          
          if($clienteid==0 && $cliente==false){

            $clientes=$this->getClientes_Select();
            return $this->render('archivos/select_clientes.html.twig',['clientes'=>$clientes]); 

          }else{
              if(!$cliente){
                 $cliente=$em->getRepository('App:Clientes','c')->find($clienteid);
              }
          }


          $nombre_carpeta=str_replace(" ","-",$cliente->getRazonSocial());
          $ruta="uploads/$nombre_carpeta";
          if(is_dir($ruta)){
            $session=$request->getSession();

            $session->set('cliente_files',$cliente);

            return $this->render('archivos/index.html.twig',['cliente'=>$cliente,'ruta'=>$ruta,'nombre_carpeta'=>$nombre_carpeta]); 

          }else{
             return $this->render('archivos/no_files.html.twig'); 
          }


    
  }


  /**
   * @Route("/carpetas/{directorio}", name="archivos_carpetas")
   */


  public function carpetas_directorio_cliente(Request $request){

   $session=$request->getSession();
   $cliente=$session->get('cliente_files');

   $nombre_carpeta=$request->get('directorio');
   $ruta='uploads/'.$nombre_carpeta;
   $scanned_directory = array_diff(scandir($ruta), array('..', '.','.DS_Store'));
   $arr_files=array();
   $arr_directories=array();
   foreach ($scanned_directory as $key => $file) {
      //Explotamos los puntos si es pdf o xml guardamos en files sino en directorios
        $explode=explode(".",$file);
        $ext=$explode[count($explode)-1];
        $ext=strtolower($ext);

        if($ext=='xml' or $ext=='pdf' or $ext=='png' || $ext=='jpg' || $ext=='jpeg'){
          $arr_files[]=$file;
        }else{
          $arr_directories[]=$file;
        }
   }
   if(count($arr_files)==0){

       return $this->render('archivos/carpetas.html.twig',['directorios'=>$arr_directories,'directorio'=>$nombre_carpeta,'cliente'=>$cliente]); 

   }


  }

    /**
   * @Route("/carpetas", name="archivos_carpetas_add_ruta")
   */


  public function carpetas_add_ruta(Request $request){
   
   $session=$request->getSession();
   $cliente=$session->get('cliente_files');

   $nombre_carpeta=$request->get('ruta');
   $ruta='uploads/'.$nombre_carpeta;
   $scanned_directory = array_diff(scandir($ruta), array('..', '.','.DS_Store'));
   $arr_files=array();
   $arr_directories=array();
   foreach ($scanned_directory as $key => $file) {
      //Explotamos los puntos si es pdf o xml guardamos en files sino en directorios
        $explode=explode(".",$file);
        $ext=$explode[count($explode)-1];
        $ext=strtolower($ext);

        if($ext=='xml' or $ext=='pdf' or $ext=='png' || $ext=='jpg' || $ext=='jpeg'){
          $arr_files[]=$file;
        }else{
          $arr_directories[]=$file;
        }
   }
   if(count($arr_files)==0){
       return $this->render('archivos/carpetas.html.twig',['directorios'=>$arr_directories,'directorio'=>$nombre_carpeta,'cliente'=>$cliente]);

   }
   else{

      return $this->render('archivos/files.html.twig',['directorios'=>$arr_directories,'directorio'=>$nombre_carpeta,'files'=>$arr_files,'cliente'=>$cliente]); 

   }

  }

    public function cliente_mes_xml(Request $request,$cliente,$mes){

    $user=($this->getUser());
    $cliente=false;
    $em=$this->getDoctrine()->getManager();
    if($user->getRoles()[0]=="ROLE_CLIENTE"){

      $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
      $razon_social=$cliente->getRazonSocial();
    }

    return $this->render('archivos/mes.html.twig',array('cliente'=>$cliente)); 
  
  }

  private function getClientes_Select(){
  
  $em=$this->getDoctrine()->getManager();
  $user=($this->getUser());

  $qb=$em->createQueryBuilder();
  $qb->select('c')->from('App:Clientes','c');
  
  if($user->getRoles()[0]=="ROLE_AUXILIAR"){
    //Buscamos el auxiliar con el mismo correo del cliente logueado
    $auxiliar=$em->getRepository('App:Auxiliares','a')->findOneByEmail($user->getEmail());
    $qb->andWhere('c.auxiliarId=:auxiliarId');
    $qb->setParameter('auxiliarId',$auxiliar->getId());
  }
  $qb->orderBy('c.razonSocial','Asc');
  $clientes=$qb->getQuery()->getResult();
  return $clientes;

}



}


