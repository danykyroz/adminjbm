<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Entity\CuentasPorCobrar;
use App\Entity\Empleados;
use App\Entity\HorarioEmpleado;
use App\Entity\Pagos;
use CfdiUtils\Cfdi;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;
use CfdiUtils\ConsultaCfdiSat\WebService;
use FOS\UserBundle\Model\UserManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\UserProviderInterface;


/**
 * @Route("/clientes")
 */

class ClientesController extends AbstractController
{

    private $userManager;
    private $paginator;
    public $session;

    public function __construct(UserManagerInterface $userManager, PaginatorInterface $paginator,SessionInterface $session){
        $this->userManager=$userManager;
        $this->paginator=$paginator;
        $this->session=$session;


    } 

  /**
   * @Route("/index", name="clientes_index")
   */

  public function index(Request $request)
  {
      // en index pagina con datos generales de la app
          $session=$request->getSession();
  	   	  $user=($this->getUser());

          $em=$this->getDoctrine()->getManager();

          $qb=$em->createQueryBuilder();
          $qb->select('c')->from('App:Clientes','c');


          //Busquedas
        if($request->get('query')!=""){

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));
            
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'razonSocial'));

            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'email'));
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }

        if($user->getRoles()[0]=="ROLE_AUXILIAR"){
          //Buscamos el auxiliar con el mismo correo del cliente logueado
          $auxiliar=$em->getRepository('App:Auxiliares','a')->findOneByEmail($user->getEmail());
          $qb->andWhere('c.auxiliarId=:auxiliarId');
          $qb->setParameter('auxiliarId',$auxiliar->getId());
        }
        //



          $qb2=$em->createQueryBuilder();
          $qb2->select('a')->from('App:Auxiliares','a')->orderBy('a.razonSocial','Desc');

          $auxiliares=$qb2->getQuery()->getResult();


        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
     
   

          return $this->render('clientes/index.html.twig',[
            'clientes' => $pagination,
            'pagination'=>$pagination,
            'auxiliares'=>$auxiliares,
            'query'=>$request->get('query',''),
          ]); 


    
  }


    /**
     * @Route("/{id}/activar", name="clientes_activar", methods={"GET","POST"})
    */
    public function activar(Request $request, Clientes $cliente): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $cliente->setEstado(1);
        $em->persist($cliente);
        $em->flush();

        return $this->redirectToRoute('clientes_index');

    }

      /**
     * @Route("/{id}/suspender", name="clientes_suspender", methods={"GET","POST"})
    */
    public function suspender(Request $request, Clientes $cliente): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $cliente->setEstado(0);
        $em->persist($cliente);
        $em->flush();

        return $this->redirectToRoute('clientes_index');

    }

  /**
     * @Route("/new", name="clientes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        
        $em=$this->getDoctrine()->getManager();

        $cliente = new Clientes();

        $id=$request->get('id');
        $documento=$request->get('documento');
        $razonSocial=$request->get('razonSocial');
        $celular=$request->get('celular');
        $direccion=$request->get('direccion');
        $email=$request->get('email');
        $auxiliar=(int) $request->get('auxiliar');

        if($id>0){
          $cliente=$em->getRepository('App:Clientes','c')->find($id);
        }
        $cliente->setDocumento($documento);
        $cliente->setEmail($email);
        $cliente->setRazonSocial($razonSocial);
        $cliente->setCelular($celular);
        $cliente->setDireccion($direccion);
        $cliente->setAuxiliarId($auxiliar);

        
        $em->persist($cliente);
        $em->flush();

        $rol="ROLE_CLIENTE";
        if($id==0){
          
          $user=$this->createUserClient($cliente,$rol);    
         
          if(!$user){
              $em->remove($cliente);
              $em->flush();
              $this->addFlash('bad', 'Ya existe un cliente o usuario con este correo');
          }
          
        }
        
        if ($cliente->getId()>0) {

                $this->addFlash('success', 'Cliente creado exitosamente!');
                return $this->redirectToRoute('clientes_index');
        }

         return $this->redirectToRoute('clientes_index');
    }


    private function createUserClient($cliente,$rol){

            $userManager = $this->userManager;
            
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository("App:User")->findOneBy(["email" => $cliente->getEmail()]);

            if (!$user) {
                /** @var  $user GrantUser */
                $expusername=explode("@",$cliente->getEmail());
                $username=$cliente->getRazonSocial();   
                $user = $userManager->createUser();
                $user->setUsername($username);
                //$user->setCreatedAt(new DateTime('now'));
                //$user->setUsernameCanonical($username);
                $user->setEmail($cliente->getEmail());
                //$user->setEmailCanonical($cliente->getEmail());
                $user->setEnabled(true);
                $user->setRoles(array($rol));
                $user->setPlainPassword($cliente->getDocumento());
                $userManager->updateUser($user);
                return true;
            }else{
                return false;
            }
    }



    /**
   * @Route("/cambiar/password", name="cambiar_password")
   */
    public function cambiar_password(Request $request){

            
            $em=$this->getDoctrine()->getManager();
            
            $fosuser=($this->getUser());
            $submit=$request->get('submit');
            
            $password=$request->get('_password');
            $password_check=$request->get('_password_check');
                
            if(strtolower($password)==strtolower($password_check)){
                $fosuser->setPlainPassword($password_check);
                $this->userManager->updateUser($fosuser);

                $this->addFlash('success', 'Contraseña reseteada exitosamente');
                
               

            }else{
                    
                $this->addFlash('bad', 'password no coinciden'); 

               
            }  

           return $this->redirect($this->generateUrl('clientes_perfil'));
    }


    /**
     * @Route("/perfil/show", name="clientes_perfil", methods={"GET"})
     */
    public function perfil(Request $request): Response
    {
        $user=($this->getUser());
        $em=$this->getDoctrine()->getManager();
        
        $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
        
        if(is_object($cliente)){

        }else{
          $cliente=false;
        }
        return $this->render('clientes/perfil.html.twig', [
            'cliente' => $cliente,
            'user'=>$user
           
        ]);
    }

     /**
     * @Route("/perfil/{id}/edit", name="perfil_edit", methods={"GET","POST"})
     */
    public function perfil_edit(Request $request): Response
    {
        
        $user=($this->getUser());

        $session=$this->session;

        $em=$this->getDoctrine()->getManager();
        
        $avatar=$_FILES['avatar'];
        
        if(is_array($avatar)){

            if($_FILES["avatar"]["error"]==0){
               $tmp_name = $_FILES["avatar"]["tmp_name"];
               $name = $_FILES["avatar"]["name"];
               $move= move_uploaded_file($tmp_name, "uploads/$name");
               if($move){
                $user->setAvatar("uploads/$name");
                $em->persist($user);
                $em->flush();

                $session->set('avatar',$user->getAvatar());

               }
            }
        }

          return $this->render('clientes/perfil.html.twig', [
            'cliente' => false,
            'user'=>$user
        ]);

    }



    /**
     * @Route("/{id}", name="clientes_show", methods={"GET"})
     */
    public function show(Clientes $cliente): Response
    {
        $user_admin=($this->getUser());
        $em=$this->getDoctrine()->getManager();
         return $this->render('clientes/show.html.twig', [
            'cliente' => $cliente,
           
        ]);
    }



    /**
     * @Route("/{id}/edit", name="clientes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Clientes $cliente): Response
    {
        $user=($this->getUser());

        $session=$this->session;

        $em=$this->getDoctrine()->getManager();
        
        $avatar=$_FILES['avatar'];
        
        if(is_array($avatar)){

            if($_FILES["avatar"]["error"]==0){
               $tmp_name = $_FILES["avatar"]["tmp_name"];
               $name = $_FILES["avatar"]["name"];
               $move= move_uploaded_file($tmp_name, "uploads/$name");
               if($move){
                $cliente->setAvatar("uploads/$name");
                $em->persist($cliente);
                $em->flush();

                $session->set('avatar',$cliente->getAvatar());

               }
            }
        }

          return $this->render('clientes/perfil.html.twig', [
            'cliente' => $cliente,
            'user'=>$user
        ]);

       
    
    }

    /**
     * @Route("/delete/{id}", name="clientes_delete", methods={"GET"})
     */
    public function delete(Request $request, Clientes $cliente): Response
    {
          
          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->remove($cliente);
          $entityManager->flush();
          
          $this->addFlash('success', 'Cliente eliminado exitosamente.');


        return $this->redirectToRoute('clientes_index');
    }



  /**
  * @Route("/pagos/index", name="pagos_clientes", methods={"GET"})
  */
  public function pagos_cliente(Request $request){
      
      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();
      $filtros=$request->get('filtros');
      $cliente=false;
      $clientes=$this->getClientes_Select();
      $clienteid=$request->get('clienteid',0);
      $exportar=$request->get('exportar','');
      $factura_interna=$request->get('factura_interna',0);

      //Preguntar si no hay id de cliente para mostrar la plantilla de clientes

      $user=($this->getUser());

      if($user->getRoles()[0]!="ROLE_CLIENTE"){

        if($clienteid==0 && @$filtros['cliente']=="" && $exportar==""){
          return $this->render('clientes/select_cliente.html.twig',array('clientes'=>$clientes,'factura_interna'=>$factura_interna)); 
        }

      }

      if($filtros==""){
        $filtros['cliente']=$clienteid;
        $filtros['tipo']="";
        $filtros['fecha_inicial']="";
        $filtros['fecha_final']="";
        $filtros['year']=date('Y');
        $filtros['mes']=date('m');
        $filtros['proveedor']="";

      }

      $user=($this->getUser());

      if($user->getRoles()[0]=="ROLE_CLIENTE"){

        $clientes=array();

        $cliente=$em->getRepository('App:Clientes','c')->findOneByEmail($user->getEmail());
        $filtros['cliente']=$cliente->getId();
        $clientes[]=array('id'=>$cliente->getId(),'razonSocial'=>$cliente->getRazonSocial());

      }

      $qb->select('p')->from('App:Pagos','p');
        
      $user=($this->getUser());

      if($user->getRoles()[0]=="ROLE_AUXILIAR"){

          $auxiliar=$em->getRepository('App:Auxiliares','a')->findOneByEmail($user->getEmail());

          $qb->innerJoin('App:Clientes','c','WITH','p.clienteId=c.id');
          $qb->andWhere('c.auxiliarId=:auxiliarId');
          $qb->setParameter('auxiliarId',$auxiliar->getId());

      }

      if(isset($filtros['cliente'])){
        if($filtros["cliente"]!=""){
           $cliente=$em->getRepository('App:Clientes','c')->find($filtros['cliente']);
         }
      }
      if($clienteid>0){
        $cliente=$em->getRepository('App:Clientes','c')->find($clienteid);
      }


      if(is_object($cliente)){
        $qb->where('p.clienteId=:clienteId')->setParameter('clienteId',$cliente->getId());
      }

      if(isset($filtros['tipo'])){
        if($filtros['tipo']!=""){
          $qb->andWhere("p.tipo=:tipo");
          $qb->setParameter('tipo',$filtros['tipo']);
        }

      }
      if($factura_interna==1){
         $qb->andWhere("p.tipoPagoId=:tipoPagoId");
         $qb->setParameter('tipoPagoId',5);
      }

      if(isset($filtros['year'])){

        if($filtros['year']!=""){
          $filtros['fecha_inicial']=date($filtros["year"].'-01-01');
          $filtros['fecha_final']=date($filtros["year"].'-12-31');
        }

      }
      if(isset($filtros['mes'])){

        if($filtros['mes']!=""){
          $mes=$filtros['mes'];
          $year=$filtros['year'];
          $filtros['fecha_inicial']=date("$year-$mes-01");
          $filtros['fecha_final']=date("$year-$mes-31");
        }
      }

      if($filtros['fecha_inicial']!=""){
          $qb->andWhere('p.fecha>=:fecha_inicial');
          $qb->setParameter('fecha_inicial',$filtros['fecha_inicial']);
      }
      if($filtros['fecha_final']){

         $qb->andWhere('p.fecha<=:fecha_final');
         $qb->setParameter('fecha_final',$filtros['fecha_final'].' 23:59:59');

      }
     

      if(isset($filtros['cliente'])){
        if($filtros["cliente"]!=""){
           $cliente=$em->getRepository('App:Clientes','c')->find($filtros['cliente']);
         }
      }

      if(isset($filtros['proveedor'])){
        if($filtros["proveedor"]!=""){
          $qb->andWhere('p.nombreProveedor=:proveedor');
          $qb->setParameter('proveedor',$filtros["proveedor"]);
         }
      }

      if($request->get('query')!=""){

            $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'p', 'id'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'p', 'valor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'p', 'folio'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'p', 'nombreProveedor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'p', 'rfcProveedor'));


            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }

        $proveedores=array();
        if(is_object($cliente)){
          
          $qbp=$em->createQueryBuilder();
          $qbp->select('p.nombreProveedor as proveedor')->from('App:Pagos','p')->where('p.clienteId=:clienteId')->andWhere("p.tipoPagoId=5");
          $qbp->setParameter('clienteId',$cliente->getId());
          $qbp->groupBy('p.nombreProveedor');
          $qbp->orderBy('p.nombreProveedor','ASC');
          $proveedores=$qbp->getQuery()->getResult();

        }
       
        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            25 /*limit per page*/
        );
     

      $tipopagos=$em->getRepository('App:TipoPago','t')->findAll();


      if($exportar==true){
        
        $pagos=$qb->getQuery()->getResult();
         $body= $this->renderView('clientes/pagos_excel.html.twig',array(
          'pagos'=>$pagos,'tipopagos'=>$tipopagos));

          $response=new Response();
          $fecha=date('Ymd_His');

          $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "cheques_y_transferencias_$fecha.xls"
        );

        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        $response->setContent($body);
        return $response;
      }

      if($factura_interna==1){
        $plantilla="clientes/pagos_intercompania.html.twig";
      }
      else{
        $plantilla='clientes/pagos.html.twig';
      }      

       
      return $this->render($plantilla,array(
      'pagos'=>$pagination,
      'cliente'=>$cliente,
      'clientes'=>$clientes,
      'pagination'=>$pagination,
      'filtros'=>$filtros,
      'tipopagos'=>$tipopagos,
      'proveedores'=>$proveedores,
      'facturaInterna' => $factura_interna,


      )); 
  }

  /**
  * @Route("/pagos/{id}/cliente/revisado", name="pagos_cliente_revisado", methods={"GET","POST"})
  */
  public function pagos_cliente_revisado(Request $request, Pagos $pago){
      
      $em=$this->getDoctrine()->getManager();
      
      if($pago->getRevisado()==1){
        $pago->setRevisado(0);
      }else{
        $pago->setRevisado(1);
      }  

      $em->persist($pago);
      $em->flush();

      return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$pago->getClienteId())));

  }


  /**
  * @Route("/pagos/factura/lote", name="pagos_facturas_lote", methods={"GET","POST"})
  */
  public function pagos_facturas_lote(Request $request){

      $em=$this->getDoctrine()->getManager();
      $clienteid=$request->get('clienteid');
      $facturaInterna = $request->get('facturaInterna');

      $cliente=$em->getRepository('App:Clientes','c')->find($clienteid);
      $cont_facturas=0;

      $file = $request->files->get('file');
      if($file->getError()==0){
       
         $file->move(
                        'uploads' . DIRECTORY_SEPARATOR .
                        'xmls' . DIRECTORY_SEPARATOR .
                        'csv'.DIRECTORY_SEPARATOR,
                         $file->getClientOriginalName()
                    );

        $ruta_csv='uploads/xmls/csv/'.$file->getClientOriginalName();
       
        $cont_linea = 0;
        //Abrimos nuestro archivo
        $archivo = fopen($ruta_csv, "r");
        //Lo recorremos
        $cont_linea = 0;
        while (($datos = fgetcsv($archivo, "")) == true) 
        {
            if(count($datos) == 1 && !strpos($datos[0], ',')){

                unlink($ruta_csv);
                $this->addFlash('warning', 'No se puede procesar el archivo debido a que no cumple el formato requerido. El archivo debe ser separado por comas ","');
                return $this->redirectToRoute('pagos_clientes', [
                    'factura_interna'=>$facturaInterna,
                    'clienteid'=>$clienteid
                ]);

                break;
            }

            if($cont_linea==0){
                $cont_linea++;
              continue;
            }
            $proveedor=$datos[0];
            $rfc=$datos[1];
            $fecha=$datos[2];
            $valor=$datos[3];
            $iva=$datos[4];
            $total=$datos[5];
            $ext="csv";
            $pago=false;

            //Creamos la factura

            //Buscamos un pago tipo 5 con el mismo id de cliente y fecha
            $fecha_time=(new \DateTime($fecha));
            $fecha_inicial=$fecha_time->format('Y-m-01');
            $fecha_final=$fecha_time->format('Y-m-31');


            $qb=$em->createQueryBuilder();

            $qb->select('p')
                ->from('App:Pagos','p')
                ->where('p.fecha>=:fecha_inicial')
                ->andWhere('p.fecha<=:fecha_final')
                ->andWhere('p.clienteId=:clienteId')
                ->andWhere('p.tipoPagoId=:tipopagoId')
                ->andWhere('p.rfcProveedor=:rfcProveedor')
                ->orderBy('p.fecha','Asc');

              $qb->setParameters(array('fecha_inicial'=>$fecha_inicial,
                                        'fecha_final'=>$fecha_final,
                                        'clienteId'=>$clienteid,
                                        'tipopagoId'=>5,
                                        'rfcProveedor'=>$rfc));


            $pagos=$qb->getQuery()->getResult();
            if(count($pagos)>0){
              $pago=$pagos[0];
            }


            if(is_object($pago)){

                //Sumar todos los pagos


                }
                else{

                  $pago=new Pagos();
                  $pago->setFecha(new \DateTime($fecha_inicial));
                  $pago->setNombreProveedor($proveedor);
                  $pago->setRfcProveedor($rfc);
                  $pago->setTipo(2);
                  $pago->setClienteId($clienteid);
                  $pago->setValor($valor);
                  $pago->setFacturado(0);
                  $pago->setPorFacturar($valor);
                  $pago->setCreatedAt(new \DateTime('now'));
                  $pago->setCreatedAt(new \DateTime('now'));
                  $pago->setTipoPagoId(5);
                  $em->persist($pago);
                  $em->flush();
                  $pagoid=$pago->getId();
                }


                $CuentasPorCobrar=new CuentasPorCobrar();
                $CuentasPorCobrar->setFecha($fecha_time);
                $CuentasPorCobrar->setCreatedAt(new \DateTime('now'));
                $CuentasPorCobrar->setUpdatedAt(new \DateTime('now'));
                $CuentasPorCobrar->setClienteId($clienteid);
                $CuentasPorCobrar->setProveedor($proveedor);
                $CuentasPorCobrar->setRfc($rfc);
                $CuentasPorCobrar->setValor($valor);
                $CuentasPorCobrar->setIva($iva);
                $CuentasPorCobrar->setTotal($total);
                $CuentasPorCobrar->setPagoId($pago->getId());
                //$CuentasPorCobrar->setNombre($name);
                $CuentasPorCobrar->setExtension($ext);
                $em->persist($CuentasPorCobrar);
                $em->flush();

                $cont_facturas++;


                $qb=$em->createQueryBuilder();

                $qb->select('SUM(c.valor) as valor, SUM(c.iva) as iva, SUM(c.total) as total')
                  ->from('App:CuentasPorCobrar','c')
                  ->where('c.fecha>=:fecha_inicial')
                  ->andWhere('c.fecha<=:fecha_final')
                  ->andWhere('c.clienteId=:clienteId')
                  ->andWhere('c.rfc=:rfcProveedor')
                  ->andWhere('c.pagoId=:pagoId');

                  $qb->setParameters(array('fecha_inicial'=>$fecha_inicial,'fecha_final'=>$fecha_final,'clienteId'=>$clienteid,'rfcProveedor'=>$rfc,'pagoId'=>$pago->getId()));


                $totales=$qb->getQuery()->getSingleResult();

                if(is_array($totales)){
                  $pago->setValor($totales['valor']);
                  $em->persist($pago);
                  $em->flush();
                  //$pago->setFacturado(0);
                  //$pago->setPorFacturar
                }

                //Completamos para sumar lo que esta revisado
                $qb->andWhere('c.comprobado=1');
                $totales_revisado=$qb->getQuery()->getSingleResult();
                if(is_array($totales_revisado)){
                  if(is_object($pago)){
                    if($totales_revisado['total']==null){
                      $total_revisado=0;
                    }
                    else{
                       $total_revisado=$totales_revisado['total'];
                    }
                    $resta=$pago->getValor()-$total_revisado;
                    $pago->setFacturado($total_revisado);
                    $pago->setPorFacturar($resta);
                    $em->persist($pago);
                    $em->flush();

                  }

                }
             $cont_linea++;

        }
        //Cerramos el archivo
        fclose($archivo);

          $this->addFlash('success', 'Archivo procesado correctamente');
      }


      return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$pago->getClienteId(),'factura_interna'=>1)));
 

  }


    /**
  * @Route("/pagos/edit/{id}", name="pagos_cliente_edit", methods={"GET","POST"})
  */
  public function pagos_cliente_edit(Request $request){
      
      $em=$this->getDoctrine()->getManager();
      $pagoid=$request->get('pagoid');
      $beneficiario=$request->get('beneficiario');
      $pago=$em->getRepository('App:Pagos','p')->find($pagoid);
      $valor=$request->get('valor');
      $valor=str_replace(",","", $valor);
      $folio=$request->get('folio');

      if($pago){
        $pago->setBeneficiario($beneficiario);
        $pago->setValor($valor);
        $pago->setFolio($folio);
        $pago->setPorFacturar($valor-$pago->getFacturado());

        $em->persist($pago);
        $em->flush();
      }
     
      return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$pago->getClienteId())));

  }

  
  /**
  * @Route("/pagos/cliente/{id}", name="pagos_cliente_id", methods={"GET"})
  */
  public function pagos_cliente_id(Request $request, Clientes $cliente){
      
      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();

      $qb->select('p')->from('App:Pagos','p')->where('p.clienteId=:clienteId')->setParameter('clienteId',$cliente->getId());


      $filtros=$request->get('filtros');


      if($filtros==""){
        $filtros['cliente']="";
        $filtros['tipo']="";
        $filtros['fecha_inicial']="";
        $filtros['fecha_final']="";
        $filtros['year']=date('Y');
        $filtros['mes']=date('m');
      }

       $user=($this->getUser());

      if($user->getRoles()[0]=="ROLE_CLIENTE"){

        $clientes=array();

        $cliente=$em->getRepository('App:Clientes','c')->findOneByEmail($user->getEmail());
        $filtros['cliente']=$cliente->getId();
        $clientes[]=array('id'=>$cliente->getId(),'razonSocial'=>$cliente->getRazonSocial());

      }

       if(is_object($cliente)){
        $qb->where('p.clienteId=:clienteId')->setParameter('clienteId',$cliente->getId());
      }


      if(isset($filtros['tipo'])){
        if($filtros['tipo']!=""){
          $qb->andWhere("p.tipo=:tipo");
          $qb->setParameter('tipo',$filtros['tipo']);
        }

      }

      if(isset($filtros['year'])){

        if($filtros['year']!=""){
          $filtros['fecha_inicial']=date($filtros["year"].'-01-01');
          $filtros['fecha_final']=date($filtros["year"].'-12-31');
        }

      }
      if(isset($filtros['mes'])){

        if($filtros['mes']!=""){
          $mes=$filtros['mes'];
          $year=$filtros['year'];
          $filtros['fecha_inicial']=date("$year-$mes-01");
          $filtros['fecha_final']=date("$year-$mes-31");
        }
      }

      if($filtros['fecha_inicial']!=""){
          $qb->andWhere('p.fecha>=:fecha_inicial');
          $qb->setParameter('fecha_inicial',$filtros['fecha_inicial']);
      }
      if($filtros['fecha_final']){
         $qb->andWhere('p.fecha<=:fecha_final');
         $qb->setParameter('fecha_final',$filtros['fecha_final'].' 23:59:59');
      }
     

      if(isset($filtros['cliente'])){
        if($filtros["cliente"]!=""){
           $cliente=$em->getRepository('App:Clientes','c')->find($filtros['cliente']);
         }
      }


     

      if($request->get('query')!=""){

            $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'valor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'folio'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'rfc'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'proveedor'));
            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }



        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
     
      $tipopagos=$em->getRepository('App:TipoPago','t')->findAll();

      return $this->render('clientes/pagos.html.twig',array(
      'pagos'=>$pagination,
      'cliente'=>$cliente,
      'pagination'=>$pagination,
      'clientes'=>$this->getClientes_Select(),
      'filtros'=>$filtros,
      'tipopagos'=>$tipopagos

      )); 
  }

  /**
  * @Route("/pagos/cliente/nuevo", name="pagos_cliente_nuevo", methods={"GET","POST"})
  */
  public function pagos_cliente_nuevo(Request $request){
    
     $em=$this->getDoctrine()->getManager();

     $pagos=new Pagos(); 
     $pagos->setFecha(new \DateTime($request->get('fecha')));
     $pagos->setTipo($request->get('tipo'));
     $pagos->setFolio($request->get('folio'));
     $pagos->setValor($request->get('valor'));
     $pagos->setBeneficiario($request->get('beneficiario'));
     $pagos->setFacturado(0);
     $pagos->setPorFacturar($request->get('valor'));
     $pagos->setClienteId($request->get('cliente'));
     $pagos->setTipoPagoId($request->get('tipopago'));
     $em->persist($pagos);
     $em->flush();

     if($_FILES["file"]["error"]==0){

          $tmp_name = $_FILES["file"]["tmp_name"];
          $name = $_FILES["file"]["name"];

          //Nombre carpeta

           $cliente=$em->getRepository('App:Clientes','c')->find($pagos->getClienteId());

            $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
            
            $year_carpeta=date_format($pagos->getFecha(),'Y');
            $mes_carpeta=date_format($pagos->getFecha(),'m');
            $dia_carpeta=date_format($pagos->getFecha(),'d');
            if($pagos->getTipo()==1){
              $tipo='cheque';
            }else{
              $tipo='transferencia';
            }


            $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/'.$tipo.'/'.$dia_carpeta.'/'.$pagos->consecutivo();
            
            $ruta_nueva=$carpeta_nueva.'/'.$name;
            $this->new_directories($carpeta_nueva);
            
            $ruta="uploads/$name";

            $move= move_uploaded_file($tmp_name, $ruta);

            $move_new= copy($ruta, $ruta_nueva);
            if($move_new){
              unlink($ruta);
            }

           if($move_new){
            $pagos->setFile($ruta_nueva);
            $em->persist($pagos);
            $em->flush();
           }
     }
   


     $this->addFlash('success', 'Pago guardado exitosamente.');


    return  $this->redirect($this->generateUrl('pagos_cliente_id',array('id'=>$request->get('cliente'))));

  }

  /**
  * @Route("/pagos/nuevo/proveedor", name="pagos_facturas_proveedor_manual", methods={"GET","POST"})
  */
  public function pagos_nuevo_proveedor(Request $request){
    
     $em=$this->getDoctrine()->getManager();

     $pagos=new Pagos(); 
     $pagos->setFecha(new \DateTime($request->get('fecha')));
     $pagos->setTipo(2);
     $pagos->setValor(0);
     $pagos->setFacturado(0);
     $pagos->setPorFacturar(0);
     $pagos->setClienteId($request->get('clienteid'));
     $pagos->setNombreProveedor($request->get('proveedor'));
     $pagos->setRfcProveedor($request->get('rfc'));
     $pagos->setTipoPagoId(5);
     $em->persist($pagos);
     $em->flush();

     /*if($_FILES["file"]["error"]==0){

          $tmp_name = $_FILES["file"]["tmp_name"];
          $name = $_FILES["file"]["name"];

          //Nombre carpeta

           $cliente=$em->getRepository('App:Clientes','c')->find($pagos->getClienteId());

            $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
            
            $year_carpeta=date_format($pagos->getFecha(),'Y');
            $mes_carpeta=date_format($pagos->getFecha(),'m');
            $dia_carpeta=date_format($pagos->getFecha(),'d');
            if($pagos->getTipo()==1){
              $tipo='cheque';
            }else{
              $tipo='transferencia';
            }


            $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/'.$tipo.'/'.$dia_carpeta.'/'.$pagos->consecutivo();
            
            $ruta_nueva=$carpeta_nueva.'/'.$name;
            $this->new_directories($carpeta_nueva);
            
            $ruta="uploads/$name";

            $move= move_uploaded_file($tmp_name, $ruta);

            $move_new= copy($ruta, $ruta_nueva);
            if($move_new){
              unlink($ruta);
            }

           if($move_new){
            $pagos->setFile($ruta_nueva);
            $em->persist($pagos);
            $em->flush();
           }
     }*/
   


     $this->addFlash('success', 'Pago guardado exitosamente.');


    return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$request->get('clienteid'),'factura_interna'=>1)));

  }


  /**
  * @Route("/cobros/new/file", name="cobros_new_file", methods={"GET","POST"})
  */
  public function cobros_new_file(Request $request){
    
     $em=$this->getDoctrine()->getManager();
     
     $cobro=$em->getRepository('App:CuentasPorCobrar','c')->find($request->get('cobroid'));
    
     if($_FILES["file"]["error"]==0){

          $tmp_name = $_FILES["file"]["tmp_name"];
          $name = $_FILES["file"]["name"];

          //Nombre carpeta

           $cliente=$em->getRepository('App:Clientes','c')->find($cobro->getClienteId());

            $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
            
            $year_carpeta=date_format($cobro->getFecha(),'Y');
            $mes_carpeta=date_format($cobro->getFecha(),'m');
            $dia_carpeta=date_format($cobro->getFecha(),'d');
           
            $tipo="facturas_por_pagar";

            if($cobro->getFolio()!=""){
              $consecutivo=$cobro->getFolio();
            }
            else{
              $consecutivo=$cobro->getId();
            }

            $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/'.$tipo.'/'.$dia_carpeta.'/'.$consecutivo;
            
            $ruta_nueva=$carpeta_nueva.'/'.$name;
            $this->new_directories($carpeta_nueva);
            
            $ruta="uploads/$name";

            $move= move_uploaded_file($tmp_name, $ruta);

            $move_new= copy($ruta, $ruta_nueva);
            if($move_new){
              unlink($ruta);
            }

           if($move_new){
            $cobro->setFile($ruta_nueva);
            $em->persist($cobro);
            $em->flush();
           }
     }
   


     $this->addFlash('success', 'Comprobante guardado exitosamente.');


      return  $this->redirect($request->server->get('HTTP_REFERER'));

  }

  /**
  * @Route("/pagos/new/file", name="pagos_new_file", methods={"GET","POST"})
  */
  public function pago_new_file(Request $request){
    
     $em=$this->getDoctrine()->getManager();

     $pagos=$em->getRepository('App:Pagos','p')->find($request->get('pagoid'));
    
     if($_FILES["file"]["error"]==0){

          $tmp_name = $_FILES["file"]["tmp_name"];
          $name = $_FILES["file"]["name"];

          //Nombre carpeta

           $cliente=$em->getRepository('App:Clientes','c')->find($pagos->getClienteId());

            $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
            
            $year_carpeta=date_format($pagos->getFecha(),'Y');
            $mes_carpeta=date_format($pagos->getFecha(),'m');
            $dia_carpeta=date_format($pagos->getFecha(),'d');
            if($pagos->getTipo()==1){
              $tipo='cheque';
            }else{
              $tipo='transferencia';
            }


            $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/'.$tipo.'/'.$dia_carpeta.'/'.$pagos->consecutivo();
            
            $ruta_nueva=$carpeta_nueva.'/'.$name;
            $this->new_directories($carpeta_nueva);
            
            $ruta="uploads/$name";

            $move= move_uploaded_file($tmp_name, $ruta);

            $move_new= copy($ruta, $ruta_nueva);
            if($move_new){
              unlink($ruta);
            }

           if($move_new){
            $pagos->setFile($ruta_nueva);
            $em->persist($pagos);
            $em->flush();
           }
     }
   


     $this->addFlash('success', 'Pago guardado exitosamente.');


      return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$pagos->getClienteId(),'factura_interna'=>1)));

  }


  /**
  * @Route("/facturas/pago/{id}", name="facturas_pago_id", methods={"GET"})
  */
  public function facturas_pago_id(Request $request, Pagos $pago){
   

      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();
      if($pago->getTipoPagoId()<=4){
        $qb->select('c')->from('App:CuentasPorCobrar','c')->where('c.pagoId=:pagoId')->setParameter('pagoId',$pago->getId());
        $qb->andWhere("c.rfc!=''");
        
      }else{
       if($pago->getTipoPagoId()==5){
            $qb->select('c')->from('App:CuentasPorCobrar','c')->where('c.pagoId=:pagoId')->setParameter('pagoId',$pago->getId())->andWhere('c.valor>0');
       }else{
        $qb->select('c')->from('App:CuentasPorCobrar','c')->where('c.pagoId=:pagoId')->setParameter('pagoId',$pago->getId());
        $qb->andWhere("c.extension!='xml'");
        
       }
        
      }
      return new Response(count($qb->getQuery()->getResult()));
    }



   /**
  * @Route("/pago/delete/{id}", name="pagos_delete", methods={"GET"})
  */
  public function pago_delete(Request $request, Pagos $pago){
   

      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();
      $clienteid=$pago->getClienteId();
      $qb->select('c')->from('App:CuentasPorCobrar','c')->where('c.pagoId=:pagoId')->setParameter('pagoId',$pago->getId());
      $cuentas=$qb->getQuery()->getResult();

      foreach($cuentas as $cuenta){
        $cuenta->setPagoId(0);
        $em->persist($cuenta);
      }
      $em->remove($pago);
      $em->flush();


    return  $this->redirect($this->generateUrl('pagos_clientes',array('clienteid'=>$clienteid)));

  }


  /**
  * @Route("/cuentas_por_cobrar/index", name="cuentas_por_cobrar_clientes", methods={"GET"})
  */
  public function cuentas_por_cobrar(Request $request){
   
     
      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();
      $qb->select('c')->from('App:CuentasPorCobrar','c');
      $pago=false;
      $user=($this->getUser());
      $clientes=$this->getClientes_Select();  
      $cliente=false;
      $filtros=$request->get('filtros');
      $exportar=$request->get('exportar','');


       $clienteid=$request->get('clienteid',0);
      //Preguntar si no hay id de cliente para mostrar la plantilla de clientes
      if($user->getRoles()[0]!="ROLE_CLIENTE"){

          if($clienteid==0 && @$filtros['cliente']=="" && $exportar=="" ){
            return $this->render('clientes/select_cliente.html.twig',array('clientes'=>$clientes)); 
          }
      }


      if($filtros==""){
        $filtros['cliente']=$clienteid;
        $filtros['tipo']="";
        $filtros['fecha_inicial']=date('Y-m-01');
        $filtros['fecha_final']=date('Y-m-d');
        $filtros['year']="";
        $filtros['mes']="";
        $filtros["proveedor"]="";
        $filtros["extensiones"]="";

      }

     
      if($user->getRoles()[0]=="ROLE_AUXILIAR"){

          $auxiliar=$em->getRepository('App:Auxiliares','a')->findOneByEmail($user->getEmail());
          $qb->innerJoin('App:Pagos','p','WITH','c.pagoId=p');
          $qb->innerJoin('App:Clientes','cl','WITH','p.clienteId=cl.id');
          $qb->andWhere('cl.auxiliarId=:auxiliarId');
          $qb->setParameter('auxiliarId',$auxiliar->getId());
      }

      $user=($this->getUser());

      if($user->getRoles()[0]=="ROLE_CLIENTE"){

        $clientes=array();

        $cliente=$em->getRepository('App:Clientes','c')->findOneByEmail($user->getEmail());
        $filtros['cliente']=$cliente->getId();
        $clientes[]=array('id'=>$cliente->getId(),'razonSocial'=>$cliente->getRazonSocial());

      }

       if(isset($filtros['cliente'])){
         if($filtros["cliente"]!=""){
          $cliente=$em->getRepository('App:Clientes','c')->find($filtros['cliente']);
          $qb->andWhere('c.clienteId=:cliente');
          $qb->setParameter('cliente',$cliente->getId());
         }
       
      }

       if(isset($filtros['tipo'])){

         if($filtros["tipo"]!=""){
          
          if($filtros['tipo']==1){
            
            $cancelable="Cancelable sin aceptación";

          }else{
            $cancelable="No Cancelable";
          }

          $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :cancelable', 'c', 'cancelable'));
          $qb->setParameter('cancelable','%'.$cancelable.'%');;
          
         }
       
      }



      if(isset($filtros['year'])){

        if($filtros['year']!=""){
          $filtros['fecha_inicial']=date($filtros["year"].'-01-01');
          $filtros['fecha_final']=date($filtros["year"].'-12-31');
        }

      }
      if(isset($filtros['mes'])){

        if($filtros['mes']!=""){
          $mes=$filtros['mes'];
          $year=$filtros['year'];
          $filtros['fecha_inicial']=date("$year-$mes-01");
          $filtros['fecha_final']=date("$year-$mes-31");
        }
      }

      if($filtros['fecha_inicial']!=""){
          $qb->andWhere('c.fecha>=:fecha_inicial');
          $qb->setParameter('fecha_inicial',$filtros['fecha_inicial']);
      }
      if($filtros['fecha_final']){

         $qb->andWhere('c.fecha<=:fecha_final');
         $qb->setParameter('fecha_final',$filtros['fecha_final'].' 23:59:59');

      }

        if($request->get('query')!=""){

            $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'valor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'folio'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'rfc'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'proveedor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'cancelable'));

            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }
        if($filtros['extensiones']=="xml"){
            $qb->andWhere("c.extension='xml'");
        }
        //Se agrega extension xml para todas las consultas


        if($filtros['proveedor']){

         $qb->andWhere('c.proveedor=:proveedor');
         $qb->setParameter('proveedor',$filtros['proveedor']);

        }
          $qbp=$em->createQueryBuilder();
          $qbp->select('c.proveedor')->from('App:CuentasPorCobrar','c')->andWhere("(c.extension='xml' OR c.extension='csv')");
          $qbp->groupBy('c.proveedor');
          $proveedores=$qbp->getQuery()->getResult();

        //Get proveedores solo del cliente

        if(is_object($cliente)){

          $qbp=$em->createQueryBuilder();
          $qbp->select('c.proveedor')->from('App:CuentasPorCobrar','c')->where('c.clienteId=:clienteId')->andWhere("(c.extension='xml' OR c.extension='csv')");
          $qbp->setParameter('clienteId',$cliente->getId());
          $qbp->groupBy('c.proveedor');
          $proveedores=$qbp->getQuery()->getResult();

        }

       
        $qb->andWhere("c.pagoId IS NULL OR c.pagoId=0");
        $qb->andWhere("c.valor>0");

        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );


      if($exportar==true){
        
        $cuentas=$qb->getQuery()->getResult();
         $body= $this->renderView('clientes/cuentas_por_cobrar_excel.html.twig',array(
          'cuentas'=>$cuentas));

          $response=new Response();
          $fecha=date('Ymd_His');

          $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "facturas_por_pagar_$fecha.xls"
        );

        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        $response->setContent($body);
        return $response;
      }

     
      return $this->render('clientes/cuentas_por_cobrar.html.twig',array(
      'cuentas'=>$pagination,
      "pago"=>$pago,
      'pagination'=>$pagination,
      'clientes'=>$clientes,
      'filtros'=>$filtros,
      'cliente'=>$cliente,
      'proveedores'=>$proveedores

      )); 
  

  }


  /**
  * @Route("/cuentas_por_cobrar/pago/{id}", name="cuentas_por_cobrar_pago_id", methods={"GET"})
  */
  public function cuentas_por_cobrar_pago_id(Request $request, Pagos $pago){
   

      $em=$this->getDoctrine()->getManager();
      $qb=$em->createQueryBuilder();
      
      $clientes=$this->getClientes_Select();  
      $filtros=$request->get('filtros');
      $cliente=false;

      if($filtros==""){

        $filtros['cliente']="";
        $filtros['tipo']="";
        $filtros['fecha_inicial']="";
        $filtros['fecha_final']="";
        $filtros['year']="";
        $filtros['mes']="";
        $filtros["proveedor"]="";
        $filtros["extensiones"]="";

      }

      $qb->select('c')->from('App:CuentasPorCobrar','c')->where('c.pagoId=:pagoId')->setParameter('pagoId',$pago->getId());
      $user=($this->getUser());

       if($user->getRoles()[0]=="ROLE_AUXILIAR"){

          $auxiliar=$em->getRepository('App:Auxiliares','a')->findOneByEmail($user->getEmail());
          $qb->innerJoin('App:Pagos','p','WITH','c.pagoId=p');
          $qb->innerJoin('App:Clientes','cl','WITH','p.clienteId=cl.id');
          $qb->andWhere('cl.auxiliarId=:auxiliarId');
          $qb->setParameter('auxiliarId',$auxiliar->getId());
      }

      if($user->getRoles()[0]=="ROLE_CLIENTE"){

        $clientes=array();

        $cliente=$em->getRepository('App:Clientes','c')->findOneByEmail($user->getEmail());
        $filtros['cliente']=$cliente->getId();
        $clientes[]=array('id'=>$cliente->getId(),'razonSocial'=>$cliente->getRazonSocial());

      }

      if(isset($filtros['cliente'])){
        if($filtros["cliente"]!=""){
          $cliente=$em->getRepository('App:Clientes','c')->find($filtros['cliente']);
          $qb->andWhere('c.clienteId=:cliente');
          $qb->setParameter('cliente',$cliente->getId());
        }
       
      }

       if(isset($filtros['year'])){

        if($filtros['year']!=""){
          $filtros['fecha_inicial']=date($filtros["year"].'-01-01');
          $filtros['fecha_final']=date($filtros["year"].'-12-31');
        }

      }
      if(isset($filtros['mes'])){

        if($filtros['mes']!=""){
          $mes=$filtros['mes'];
          $year=$filtros['year'];
          $filtros['fecha_inicial']=date("$year-$mes-01");
          $filtros['fecha_final']=date("$year-$mes-31");
        }
      }

      if($filtros['fecha_inicial']!=""){
          $qb->andWhere('c.fecha>=:fecha_inicial');
          $qb->setParameter('fecha_inicial',$filtros['fecha_inicial']);
      }
      if($filtros['fecha_final']){

         $qb->andWhere('c.fecha<=:fecha_final');
         $qb->setParameter('fecha_final',$filtros['fecha_final'].' 23:59:59');

      }

       if(isset($filtros['tipo'])){
        
         if($filtros["tipo"]!=""){
          
          if($filtros['tipo']==1){
            
            $cancelable="Cancelable sin aceptación";

          }else{
            $cancelable="No Cancelable";
          }

          $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :cancelable', 'c', 'cancelable'));
          $qb->setParameter('cancelable','%'.$cancelable.'%');;
          
         }
       
      }


        if($request->get('query')!=""){

            $qb->andWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'id'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'valor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'folio'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'rfc'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'proveedor'));
            $qb->orWhere(sprintf('LOWER(%s.%s) LIKE :fuzzy_query', 'c', 'cancelable'));

            
            $lowerSearchQuery=trim(strtolower($request->get('query')));
            $qb->setParameter('fuzzy_query','%'.$lowerSearchQuery.'%');;

        }
        //Se agrega extension xml para todas las consultas
        if($pago->getTipoPagoId()<=4){
          $qb->andWhere("c.extension='xml'");
        }else{

            if($pago->getTipoPagoId()==5){

                $qb->andWhere("c.valor>0");

            }else{
               $qb->andWhere("c.extension!='xml'");
            }
           
        }
       

        if($filtros['proveedor']){

         $qb->andWhere('c.proveedor=:proveedor');
         $qb->setParameter('proveedor',$filtros['proveedor']);

        }
          $qbp=$em->createQueryBuilder();
          $qbp->select('c.proveedor')->from('App:CuentasPorCobrar','c')->andWhere("(c.extension='xml' OR c.extension='csv')");
          $qbp->groupBy('c.proveedor');
          $proveedores=$qbp->getQuery()->getResult();

        //Get proveedores solo del cliente

        if(is_object($cliente)){

          $qbp=$em->createQueryBuilder();
          $qbp->select('c.proveedor')->from('App:CuentasPorCobrar','c')->where('c.clienteId=:clienteId')->andWhere("(c.extension='xml' OR c.extension='csv')");
        
          $qbp->setParameter('clienteId',$cliente->getId());
          $qbp->groupBy('c.proveedor');
          $proveedores=$qbp->getQuery()->getResult();

        }

        $paginator  = $this->paginator;
        $pagination = $paginator->paginate(
            $qb, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            50 /*limit per page*/
        );
     

      return $this->render('clientes/cuentas_por_cobrar.html.twig',array(
      'cuentas'=>$pagination,
      'pago'=>$pago,
      'pagination'=>$pagination,
      'clientes'=>$clientes,
      'filtros'=>$filtros,
      'cliente'=>$cliente,
      'proveedores'=>$proveedores


      )); 
  

  }

  /**
  * @Route("/cuentas_por_cobrar/subir_archivos", name="cuentas_por_cobrar_subir_archivos", methods={"GET"})
  */
  public function subir_archivos(Request $request){
      
      $em=$this->getDoctrine()->getManager();

      $clienteId=$request->get('clienteid');
      $pagoid=$request->get('id',0);
      $cuentaid=$request->get('cuentaid',0);

      $clientes=$this->getClientes_Select();  
      
      $user=($this->getUser());
      if($user->getRoles()[0]=="ROLE_CLIENTE"){
          
          $cliente=$em->getRepository('App:Clientes','c')->findOneByEmail($user->getEmail());
         
          return $this->render('clientes/subir_archivos.html.twig',array(
          'clienteId'=>$cliente->getId(),
          'pagoid'=>$pagoid,
          'cuentaid'=>$cuentaid
          )); 
      }

     if($request->get('clienteid')=="" && $pagoid==0){
         
         return $this->render('clientes/select_cliente_cuentas.html.twig',array('clientes'=>$clientes)); 
      }else{
        if($pagoid>0){
            $pago=$em->getRepository("App:Pagos","p")->find($pagoid);
            $clienteId=$pago->getClienteId();
        }
      }


      return $this->render('clientes/subir_archivos.html.twig',array(
      'clienteId'=>$clienteId,
      'pagoid'=>$pagoid,
      'cuentaid'=>$cuentaid
      )); 
  
  }


   /**
  * @Route("/cuentas_por_cobrar/link/pdf/{id}", name="cuentas_por_cobrar_link_pdf", methods={"GET"})
  */
  public function cuentas_por_cobrar_link_pdf(Request $request, CuentasPorCobrar $cuenta){

    $em=$this->getDoctrine()->getManager();
    $name=str_replace('.xml', '.pdf',$cuenta->getNombre());

    $qb=$em->createQueryBuilder()->select('c')->from('App:CuentasPorCobrar','c')->where('c.nombre=:nombre')->andWhere('c.pagoId=:pagoId');
    $qb->setParameters(array('nombre'=>$name,'pagoId'=>$cuenta->getPagoId()));

    $result=$qb->getQuery()->getResult();
    $pdf=false;
    if(count($result)>0){
      $pdf=$result[0];
    }
    if($pdf){
      return $this->render('clientes/link_pdf.html.twig',array('pdf'=>$pdf));
    }else{
      return new Response('');
    }

  }

   /**
  * @Route("/cuentas_por_cobrar/link/file/{id}", name="cuentas_por_cobrar_link_file", methods={"GET"})
  */
  public function cuentas_por_cobrar_link_file(Request $request, CuentasPorCobrar $cuenta){

    $em=$this->getDoctrine()->getManager();
    
    if($cuenta->getFile()!=""){
      return $this->render('clientes/link_pdf.html.twig',array('pdf'=>$cuenta->getFile()));
    }else{
      return new Response($cuenta->getId());
    }

  }



  /**
  * @Route("/cuentas_por_cobrar/pago/manual/{pagoid}", name="cuentas_por_cobrar_pago_manual", methods={"POST"})
  */
  public function cuentas_por_cobrar_pago_manual(Request $request){

      $em=$this->getDoctrine()->getManager();

      $valor=$request->get('valor');
      $iva=$request->get('iva');
      $valor=str_replace(",","",$valor);
      $iva=str_replace(",","",$iva);
      $total=$valor+$iva;
      $pagoid=$request->get('pagoid');
      $pago=$em->getRepository('App:Pagos','p')->find($pagoid);

      $CuentasPorCobrar=new CuentasPorCobrar();
      $CuentasPorCobrar->setFecha(new \DateTime('now'));
      $CuentasPorCobrar->setCreatedAt(new \DateTime('now'));
      $CuentasPorCobrar->setUpdatedAt(new \DateTime('now'));
      $CuentasPorCobrar->setClienteId($pago->getClienteId());
      $CuentasPorCobrar->setValor($valor);
      $CuentasPorCobrar->setIva($iva);
      $CuentasPorCobrar->setTotal($valor);
      $CuentasPorCobrar->setExtension('csv');
      $CuentasPorCobrar->setRfc($pago->getRfcProveedor());
      $CuentasPorCobrar->setProveedor($pago->getNombreProveedor());
      $CuentasPorCobrar->setPagoId($pagoid);

      $em->persist($CuentasPorCobrar);
      $em->flush();

      $qb=$em->createQueryBuilder();

      $qb->select('SUM(c.valor) as valor, SUM(c.iva) as iva, SUM(c.total) as total')
                  ->from('App:CuentasPorCobrar','c')
                  ->andWhere('c.pagoId=:pagoId');

      $qb->setParameters(array('pagoId'=>$pago->getId()));
        

      $totales=$qb->getQuery()->getSingleResult();

      if(is_array($totales)){
        $pago->setValor($totales['valor']);
        $em->persist($pago);
        $em->flush();
        //$pago->setFacturado(0);
        //$pago->setPorFacturar
      }






    return  $this->redirect($this->generateUrl('cuentas_por_cobrar_pago_id',array('id'=>$pagoid)));


  }


   /**
  * @Route("/cuentas_por_cobrar/delete/{id}", name="cuentas_por_cobrar_delete", methods={"GET"})
  */
  public function cuentas_por_cobrar_delete(Request $request, CuentasPorCobrar $cuenta){

    $em=$this->getDoctrine()->getManager();
    $name=str_replace('.xml', '.pdf',$cuenta->getNombre());
    $clienteid=$cuenta->getClienteId();
    $pagoid=(int)$cuenta->getPagoId();

    $qb=$em->createQueryBuilder()->select('c')->from('App:CuentasPorCobrar','c')->where('c.nombre=:nombre')->andWhere('c.pagoId=:pagoId');
    $qb->setParameters(array('nombre'=>$name,'pagoId'=>$cuenta->getPagoId()));

    $result=$qb->getQuery()->getResult();
    $pdf=false;
    if(count($result)>0){
      $pdf=$result[0];
    }
    if($pdf){
     $em->remove($pdf);
    }
    $em->remove($cuenta);
    $em->flush();

    if($pagoid>0){
          return  $this->redirect($this->generateUrl('cuentas_por_cobrar_pago_id',array('id'=>$pagoid)));

    }else{
          return  $this->redirect($this->generateUrl('cuentas_por_cobrar',array('clienteid'=>$clienteid)));

    }



  }



  /**
  * @Route("/sincronizar/comprobante", name="sincronizar_comprobante", methods={"POST"})
  */
  public function sincronizar_comprobante(Request $request){

      $em=$this->getDoctrine()->getManager();
      $id=$request->get('id');
      $strpago=$request->get('pago');
      $pago_id=(int) substr($strpago,-3);
      $pago=$em->getRepository("App:Pagos","p")->find($pago_id);
      
      if($pago){
        
        $comprobante=$em->getRepository("App:CuentasPorCobrar",'c')->find($id);
        $comprobante->setPagoId($pago->getId());
        $em->persist($comprobante);
        $em->flush();

        //Sumar las transacciones
        $qb=$em->createQueryBuilder();
        $qb->select("sum(c.total) as suma")->from('App:CuentasPorCobrar','c')->where("c.pagoId=:pagoId");
        $qb->setParameter("pagoId",$pago_id);

        $total=$qb->getQuery()->getSingleScalarResult();
        $resta=$pago->getValor()-$total;
        if($resta<0){
                return  new Response('La factura supera el saldo por facturar');
        }

        $pago->setFacturado($total);
        $pago->setPorFacturar($resta);
        $em->persist($pago);
        $em->flush();

        $name=str_replace('.xml', '.pdf',$comprobante->getNombre());

        $qb=$em->createQueryBuilder()->select('c')->from('App:CuentasPorCobrar','c')->where('c.nombre=:nombre');
        $qb->andWhere('c.clienteId=:clienteId');
        $qb->setParameters(array('nombre'=>$name,'clienteId'=>$comprobante->getClienteId()));

        $result=$qb->getQuery()->getResult();
        if(count($result)>0){
          $pdf=$result[0];
          $pdf->setPagoId($pago->getId());
          $em->persist($pdf);
          $em->flush();
        }

        $mensaje="ok";

      }else{
        $mensaje="Pago no existe";
      }
    
      return  new Response($mensaje);
  }



  /**
  * @Route("/detalle/factura/{id}", name="detalle_factura", methods={"GET"})
  */
  public function detalle_factura(Request $request, $id){

    $em=$this->getDoctrine()->getManager();
    
    $factura=$em->getRepository("App:CuentasPorCobrar",'c')->find($id);
    $detalle_factura=json_decode($factura->getResponseJson());
    $nombre=$factura->getXml();
    $explode=explode(".",$nombre);
    $ext=$explode[count($explode)-1];
    $is_file=$request->get('file','');

    if($is_file==""){
         if($ext=="xml"){
            return $this->render('clientes/detalle_factura.html.twig',array('factura'=>$detalle_factura));
          }else{
              return $this->render('clientes/detalle_factura_pdf.html.twig',array('pdf'=>$nombre));
          }
    }
    else{
        $nombre=$factura->getFile();
        return $this->render('clientes/detalle_factura_pdf.html.twig',array('pdf'=>$nombre));
    }


  }


     /**
  * @Route("/nueva/cuenta_por_cobrar", name="nueva_cuenta_por_cobrar", methods={"GET","POST"})
  */
  public function nueva_cuenta_por_cobrar(Request $request){

      //$id=$request->get('id');
      $em=$this->getDoctrine()->getManager();
      $pagoid=$request->get('pagoid',0);
      $pago=false;
      $valor=$request->get('valor');
      $folio=$request->get('folio');
      $valor=str_replace(",","",$valor);

      if(isset($_FILES['file'])){
        
       $tmp_name = $_FILES["file"]["tmp_name"];
       $name = $_FILES["file"]["name"];
       $move= move_uploaded_file($tmp_name, "uploads/xmls/$name");

       if($move){
        
        $ruta="uploads/xmls/$name";
        $explode=explode(".",$name);
        $ext=$explode[count($explode)-1];
      
        if($ext!="xml"){

            if($pagoid>0){
              $pago=$em->getRepository('App:Pagos','p')->find($pagoid);
            }
            if(is_object($pago)){

              $CuentasPorCobrar=new CuentasPorCobrar();
              $CuentasPorCobrar->setFolio($folio);
              $CuentasPorCobrar->setFecha(new \DateTime('now'));
              $CuentasPorCobrar->setCreatedAt(new \DateTime('now'));
              $CuentasPorCobrar->setUpdatedAt(new \DateTime('now'));
              $CuentasPorCobrar->setClienteId($pago->getClienteId());
              $CuentasPorCobrar->setValor($valor);
              $CuentasPorCobrar->setIva(0);
              $CuentasPorCobrar->setTotal($valor);
              $CuentasPorCobrar->setNombre($name);
              $CuentasPorCobrar->setExtension($ext);

              if($pagoid>0){
                $CuentasPorCobrar->setPagoId($pagoid);
              }

              $cliente=$em->getRepository('App:Clientes','c')->find($pago->getClienteId());

              $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
              
              $year_carpeta=date_format($CuentasPorCobrar->getFecha(),'Y');
              $mes_carpeta=date_format($CuentasPorCobrar->getFecha(),'m');
              $dia_carpeta=date_format($CuentasPorCobrar->getFecha(),'d');
             
              $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/facturas_por_pagar/'.$dia_carpeta.'/'.$folio;
              
              $ruta_nueva=$carpeta_nueva.'/'.$name;
              $this->new_directories($carpeta_nueva);
            
              $move_new= copy($ruta, $ruta_nueva);
              if($move_new){
                unlink($ruta);
              }

             if($move_new){
              $CuentasPorCobrar->setXml($ruta_nueva);
              $em->persist($CuentasPorCobrar);
              $em->flush();
             }


              $qb=$em->createQueryBuilder();
              $qb->select("sum(c.total) as suma")->from('App:CuentasPorCobrar','c')->where("c.pagoId=:pagoId");
              $qb->setParameter("pagoId",$pagoid);

              $total=$qb->getQuery()->getSingleScalarResult();
              $resta=$pago->getValor()-$total;
              if($resta<0){
                      return  new Response('La factura supera el saldo por facturar');
              }

              $pago->setFacturado($total);
              $pago->setPorFacturar($resta);
              $em->persist($pago);
              $em->flush();


             return  $this->redirect($this->generateUrl('pagos_clientes'));

            }
           

        
        }else{
            
           
            
        }
    


       }
      
      }

  }


   /**
  * @Route("/cuentas_por_cobrar/analizar_archivos/{clienteid}", name="cuentas_por_cobrar_analizar_archivos", methods={"GET","POST"})
  */
  public function analizar_archivos(Request $request){

      //$id=$request->get('id');
      $em=$this->getDoctrine()->getManager();  
      
      $clienteId=$request->get('clienteid');
      $pagoid=$request->get('pagoid',0);
      $cuentaid=$request->get('cuentaid',0);
      $pago=false;

      $cliente=$em->getRepository('App:Clientes','c')->find($clienteId);

      if(isset($_FILES['file'])){
        
       $tmp_name = $_FILES["file"]["tmp_name"];
       $name = $_FILES["file"]["name"];
       $move= move_uploaded_file($tmp_name, "uploads/xmls/$name");

       if($move){
        
        $ruta="uploads/xmls/$name";
        $explode=explode(".",$name);
        $ext=$explode[count($explode)-1];
      
        if($ext=="xml"){

            $json_xml=$this->parse_xml_cfid($ruta);
            $data_json=json_decode($json_xml);

            
              $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
              
              $expfecha=explode("T",$data_json->Comprobante->Fecha);
               $fecha=$expfecha[0].' '.$expfecha[1];
         
            // los datos del cfdi que se van a consultar

            //Validamos que el receptor rfc sea el mismo del cliente
            $rfc_cliente=(String)$cliente->getDocumento();
            $rfc_receptor=(String)$data_json->Receptor->Rfc;
            if($rfc_cliente!=$rfc_receptor){

                unlink($ruta);
                 return new Response("Rfc archivo no coincide con el del cliente ".$data_json->Receptor->Rfc);
            }

            $qb=$em->createQueryBuilder();
            $qb->select("c.rfc")->from('App:CuentasPorCobrar','c')->where("c.uuid=:uuid");
            $qb->andWhere('c.clienteId=:clienteId');
            $qb->setParameter("uuid",$data_json->TimbreFiscalDigital->UUID);
            $qb->setParameter("clienteId",$clienteId);

            $result_existe=$qb->getQuery()->getResult();


            $cfdi = Cfdi::newFromString(file_get_contents($ruta));
            $request = RequestParameters::createFromCfdi($cfdi);

            $service = new WebService();
            $response = $service->request($request);
            if($cuentaid==0){

              $CuentasPorCobrar=new CuentasPorCobrar();
              $dtfecha= new \DateTime($fecha);

              $CuentasPorCobrar->setFecha($dtfecha);
              $CuentasPorCobrar->setCreatedAt(new \DateTime('now'));
              $CuentasPorCobrar->setUpdatedAt(new \DateTime('now'));
              $CuentasPorCobrar->setProveedor($data_json->Emisor->Nombre);
              $new=true;
            }else{
              
              $CuentasPorCobrar=$em->getRepository('App:CuentasPorCobrar','c')->find($cuentaid);
              $pagoid=$CuentasPorCobrar->getPagoId();
              $new=false;

            }

            if(count($result_existe)>0){

              unlink($ruta);
              $CuentasPorCobrar->setNombre($name);
              $this->deletePdfXml($CuentasPorCobrar);
              return new Response("UUID #{$data_json->TimbreFiscalDigital->UUID} ya existe");

            }

            if(!$new){

              $total_bd=(int) $CuentasPorCobrar->getTotal()+1;
              $total_xml=(int) $data_json->Comprobante->Total;
              
              $resta=$total_xml-$total_bd;

              if($total_bd!=$total_xml && ($resta>1)){

                  $CuentasPorCobrar->setNombre($name);
                  $this->deletePdfXml($CuentasPorCobrar);
                  return  new Response('El valor total del xml no coincide con el original'.$total_bd.'!='.$total_xml);
              }
            }

            $CuentasPorCobrar->setRfc($data_json->Emisor->Rfc);
            $CuentasPorCobrar->setValor($data_json->Comprobante->SubTotal);
            if(isset($data_json->Traslado)){
                $CuentasPorCobrar->setIva($data_json->Traslado->Importe);
            }else{
                $CuentasPorCobrar->setIva(0);
            }

            $CuentasPorCobrar->setDescuento($data_json->Comprobante->Descuento);
            $CuentasPorCobrar->setTotal($data_json->Comprobante->Total);
            $CuentasPorCobrar->setNombre($name);
            $CuentasPorCobrar->setExtension($ext);

            if($response->getCode()=="S - Comprobante obtenido satisfactoriamente."){
              $CuentasPorCobrar->setComprobado(1);
            }
            
            $CuentasPorCobrar->setCode($response->getCode());
            $CuentasPorCobrar->setCfdi($response->getCfdi());
            $CuentasPorCobrar->setCancelable($response->getCancellable());
            $CuentasPorCobrar->setEstadoCancelacion($response->getCancellationStatus());
            $CuentasPorCobrar->setResponseJson($json_xml);
            $CuentasPorCobrar->setClienteId($clienteId);
            $CuentasPorCobrar->setXml($ruta);
            $CuentasPorCobrar->setFolio($data_json->Comprobante->Folio);
            $CuentasPorCobrar->setUuid($data_json->TimbreFiscalDigital->UUID);

            if($pagoid>0){
              $CuentasPorCobrar->setPagoId($pagoid);
              $pago=$em->getRepository('App:Pagos','p')->find($pagoid);
            }
            $em->persist($CuentasPorCobrar);
            $em->flush();

              $year_carpeta=date_format($CuentasPorCobrar->getFecha(),'Y');
              $mes_carpeta=date_format($CuentasPorCobrar->getFecha(),'m');
              $dia_carpeta=date_format($CuentasPorCobrar->getFecha(),'d');
              $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());

              $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/facturas_por_pagar/'.$dia_carpeta.'/'.$data_json->TimbreFiscalDigital->UUID;
              
              $ruta_nueva=$carpeta_nueva.'/'.$name;


             $this->new_directories($carpeta_nueva);
            
              $move_new= copy($ruta, $ruta_nueva);
              if($move_new){
                unlink($ruta);
                $CuentasPorCobrar->setXml($ruta_nueva);
                $em->persist($CuentasPorCobrar);
                $em->flush();

              }
              

            if($pago){

              if($data_json->Comprobante->TipoDeComprobante=='E'){

                $valor=$pago->getValor()+$CuentasPorCobrar->getTotal();
                $resta=$valor-$pago->getFacturado();
                
                $pago->setValor($valor);
                $pago->setPorFacturar($resta);
            

              }else{

              
              $qb=$em->createQueryBuilder();
              $qb->select("sum(c.total) as suma")->from('App:CuentasPorCobrar','c')->where("c.pagoId=:pagoId and c.extension!='csv'");

              $qb->setParameter("pagoId",$pagoid);

              $total=$qb->getQuery()->getSingleScalarResult();
              $resta=$pago->getValor()-$total;

              $pago->setFacturado($total);
              $pago->setPorFacturar($resta);
            

              }

              if($resta<0){

               $this-> deletePdfXml($CuentasPorCobrar);

                $em->remove($CuentasPorCobrar);
                $em->flush();


                return  new Response('La factura supera el saldo por facturar');
              }

              
              $em->persist($pago);
              $em->flush();

              
            }


        }else{
            
            if($cuentaid>0){
              $CuentasPorCobrarCsv=$em->getRepository('App:CuentasPorCobrar','c')->find($cuentaid);
              $pagoid=$CuentasPorCobrarCsv->getPagoId();
            }
            
            $CuentasPorCobrar=new CuentasPorCobrar();
            $CuentasPorCobrar->setFolio(time());
            $CuentasPorCobrar->setFecha(new \DateTime('now'));
            $CuentasPorCobrar->setCreatedAt(new \DateTime('now'));
            $CuentasPorCobrar->setUpdatedAt(new \DateTime('now'));
            $CuentasPorCobrar->setClienteId($clienteId);
            $CuentasPorCobrar->setValor(0);
            $CuentasPorCobrar->setIva(0);
            $CuentasPorCobrar->setTotal(0);
            $CuentasPorCobrar->setDescuento(0);
            $CuentasPorCobrar->setNombre($name);
            $CuentasPorCobrar->setExtension($ext);


            if($pagoid>0){
              $CuentasPorCobrar->setPagoId($pagoid);
            }

            $CuentasPorCobrar->setXml($ruta);
           
            $em->persist($CuentasPorCobrar);
            $em->flush();

             $nombre_cliente=str_replace(" ","-",$cliente->getRazonSocial());
              
              $year_carpeta=date_format($CuentasPorCobrar->getFecha(),'Y');
              $mes_carpeta=date_format($CuentasPorCobrar->getFecha(),'m');
              $dia_carpeta=date_format($CuentasPorCobrar->getFecha(),'d');
             
              $carpeta_nueva='uploads/'.$nombre_cliente.'/'.$year_carpeta.'/'.$mes_carpeta.'/'.'facturas_por_pagar/'.$dia_carpeta.'/'.$CuentasPorCobrar->getFolio();
              
              $ruta_nueva=$carpeta_nueva.'/'.$name;

               $this->new_directories($carpeta_nueva);
            
              $move_new= copy($ruta, $ruta_nueva);

              if($move_new){

                 $CuentasPorCobrar->setXml($ruta_nueva);
                 $em->persist($CuentasPorCobrar);
                 $em->flush();
                 unlink($ruta);
              }

            
        }
    

        return new Response("Ok");

       }
      
      }

  }

  private function deletePdfXml($cuenta){
    
    $em=$this->getDoctrine()->getManager();  
    
    $name=str_replace('.xml', '.pdf',$cuenta->getNombre());

    $qb=$em->createQueryBuilder()->select('c')->from('App:CuentasPorCobrar','c')->where('c.nombre=:nombre')->andWhere('c.pagoId=:pagoId');
    $qb->setParameters(array('nombre'=>$name,'pagoId'=>$cuenta->getPagoId()));

    $result=$qb->getQuery()->getResult();
    $pdf=false;
    if(count($result)>0){
      $pdf=$result[0];
    }
    if($pdf){
      $em->remove($pdf);
      $em->flush();
      return true;
    }else{
      return false;
    }


  }

  private function new_directories($ruta){

    $exp=explode('/',$ruta);
    $str_file="";
    foreach ($exp as $key => $value) {
      $str_file.=$value.'/';
      @mkdir($str_file);
      chmod($str_file, 0777);
    }

    return $str_file;
  }



  /**
  * @Route("/nomina/index", name="clientes_nomina", methods={"GET"})
  */

  public function nomina(Request $request){

      $user=($this->getUser());
      $em=$this->getDoctrine()->getManager();

      $week=$request->get('week')?$request->get('week'):date("W");
      $year=$request->get('year')?$request->get('year'):date("Y");
      $days = $this->getDaysInWeek($year, $week);

      $fechas=$this->get_dates_week($year, $week);


      if($user->getRoles()[0]=="ROLE_CLIENTE"){


            $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
            $empleados=$em->getRepository('App:Empleados','e')->findBy(array('clienteId'=>$cliente->getId(),'estado'=>1));

            $arr_empleados=array();
            foreach($empleados as $empleado){

              $horario=$this->getHorarioEmpleadoSemana($year,$week,$empleado->getId());
              if($horario){
                $arr_empleados[]=array('empleado'=>$empleado,'horario'=>json_decode($horario->getDias()), 'confirmado' => $horario->getConfirmado());
              }else{
                $arr_empleados[]=array('empleado'=>$empleado,'horario'=>$empleado->getDiasDescanso(), 'confirmado' => false);
              }

            }

              $data=array(
                'cliente'=>$cliente,
                'year'=>$year,
                'week'=>$week,
                'fechas'=>$fechas,
                'empleados'=>$arr_empleados,
                'days'=>$days
              );

              $exportar=$request->get('exportar','');

              if($exportar==true){

                $body= $this->renderView('clientes/nomina_excel.html.twig',$data);

                $response=new Response();
                $fecha=date('Ymd_His');

                $dispositionHeader = $response->headers->makeDisposition(
                  ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                  "nomina_$fecha.xls"
              );

               $response->setContent($body);


              $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
              $response->headers->set('Pragma', 'public');
              $response->headers->set('Cache-Control', 'maxage=1');
              $response->headers->set('Content-Disposition', $dispositionHeader);
              
              return $response;

            }  
            
            return $this->render('clientes/nomina.html.twig',$data);

       }


       $clientes=$this->getClientes_Select();
       
       return $this->render('clientes/nomina_select.html.twig',array('clientes'=>$clientes)); 
  }

  /**
  * @Route("/nomina/guardar/empleado", name="clientes_nomina_guardar_empleado", methods={"POST"})
  */
  
  public function nomina_guardar_empleado(Request $request){


      $user=($this->getUser());
    $em=$this->getDoctrine()->getManager();
    $empleado=new Empleados();
    $empleado->setCreatedAt(new \DateTime('now'));
    $empleado->setDocumento($request->get('documento'));
    $empleado->setNombres($request->get('nombres'));
    $empleado->setCargo($request->get('cargo'));
    $empleado->setClienteId($request->get('clienteid'));
    $dias=$this->getDias($request->get('diasdescanso'));
    $dias=json_encode($dias);
    $empleado->setDiasDescanso($dias);
        $fechaAlta = $request->request->get('fecha-alta');
        $empleado->setFechaAlta(new \DateTime($fechaAlta));
    $em->persist($empleado);
    $em->flush();

    $documentoAlta = $request->files->get('documento-alta');

      if($documentoAlta instanceof UploadedFile) {

          $clienteObj = $em->getRepository(Clientes::class)->findOneById($empleado->getClienteId());

          $cliente_path = str_replace(" ","-", $clienteObj->getRazonSocial());
          $empleado_path = str_replace(" ","-", $empleado->getNombres());

          try {

              $pathAlta ='uploads' . DIRECTORY_SEPARATOR .
                  $cliente_path . DIRECTORY_SEPARATOR .
                  'nomina' . DIRECTORY_SEPARATOR .
                  $empleado_path . DIRECTORY_SEPARATOR .
                  'alta' . DIRECTORY_SEPARATOR .
                    $documentoAlta->getClientOriginalName()
              ;

              $documentoAlta->move(
                  $pathAlta
              );

              $empleado->setFileAlta($pathAlta);
              $em->persist($empleado);
              $em->flush();

          } catch (FileException $e) {
              // dd($e->getMessage());
          }
      }

    $cliente=$em->getRepository('App:Clientes','c')->find($request->get('clienteid'));

    $empleados=$em->getRepository('App:Empleados','e')->findBy(array('clienteId'=>$cliente->getId()));

    $this->addFlash('success', 'Cliente creado exitosamente!');
    
    return $this->redirectToRoute('clientes_nomina_list',array('clienteid'=>$cliente->getId()));
    
    //return $this->render('clientes_nomina',array('empleados'=>$empleados,'cliente'=>$cliente)); 

  }

  public function getDias($dias){

    $dias_semana=array('L'=>'A','M'=>'A','MI'=>'A','J'=>'A','V'=>'A','S'=>'A','D'=>'A');

    foreach ($dias as $key => $value) {
      $dias_semana[$value]='D';
    }

    return $dias_semana;

  }



  /**
  * @Route("/nomina/list", name="clientes_nomina_list", methods={"GET"})
  */
  
  public function nomina_list(Request $request){
      
    $user=($this->getUser());
    $em=$this->getDoctrine()->getManager();
    if($user->getRoles()[0]=="ROLE_CLIENTE"){
      $cliente=$em->getRepository('App:Clientes','c')->findOneBy(array('email'=>$user->getEmail()));
    }else{
      $cliente=$em->getRepository('App:Clientes','c')->find($request->get('clienteid'));
    }

    $week=$request->get('week')?$request->get('week'):date("W");
    $year=$request->get('year')?$request->get('year'):date("Y");

    $fechas=$this->get_dates_week($year, $week);
    $days = $this->getDaysInWeek($year, $week);

    $empleados=$em->getRepository('App:Empleados','e')->findBy(array('clienteId'=>$cliente->getId()));

    $arr_empleados=array();
    foreach($empleados as $empleado){

      $horario=$this->getHorarioEmpleadoSemana($year,$week,$empleado->getId());
      if($horario){
        $arr_empleados[]=array('empleado'=>$empleado,'horario'=>json_decode($horario->getDias()), 'confirmado' => $horario->getConfirmado());
      }else{
        $arr_empleados[]=array('empleado'=>$empleado,'horario'=>$empleado->getDiasDescanso(), 'confirmado' => false);
      }

    }

    $data=array('empleados'=>$arr_empleados,
                'cliente'=>$cliente,
                'year'=>$year,
                'week'=>$week,
                'days' => $days,
                'fechas'=>$fechas);


    $exportar=$request->get('exportar','');

      if($exportar==true){

        $body= $this->renderView('clientes/nomina_excel.html.twig',$data);

        $response=new Response();
        $fecha=date('Ymd_His');

        $dispositionHeader = $response->headers->makeDisposition(
          ResponseHeaderBag::DISPOSITION_ATTACHMENT,
          "nomina_$fecha.xls"
      );

       $response->setContent($body);


      $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);
      
      return $response;

    }  

    return $this->render('clientes/nomina.html.twig',$data); 
  

  }

  private function getHorarioEmpleadoSemana($year,$semana,$empleadoid){

    $em=$this->getDoctrine()->getManager();
    $horario=false;
    $qb=$em->createQueryBuilder();
   
    $qb->select('h')->from('App:HorarioEmpleado','h')
                    ->where('h.empleadoId=:empleado')
                    ->andWhere('h.year=:year')
                    ->andWhere('h.semana=:semana');

    $qb->setParameter('empleado',$empleadoid);
    $qb->setParameter('year',$year);
    $qb->setParameter('semana',$semana);
    
    $qb->orderBy('h.createdAt','Desc');
 
    $horarios=$qb->getQuery()->getResult();
    if(count($horarios)>0){
      $horario=($horarios[0]);
    }

    return $horario;
   

  }



  /**
  * @Route("/nomina/guardar_horario", name="clientes_nomina_guardar_horario", methods={"POST"})
  */
  
  public function nomina_guardar_horario(Request $request){
      
    $em=$this->getDoctrine()->getManager();

    $clienteid=$request->get('clienteid');
    $empleado=$request->get('empleado');
    $year=$request->get('year');
    $semana=(int)$request->get('semana');
    $dias=$request->get('dias');

    $HorarioEmpleado=new HorarioEmpleado();
    $HorarioEmpleado->setCreatedAt(new \DateTime());
    $HorarioEmpleado->setYear($year);
    $HorarioEmpleado->setSemana($semana);
    $HorarioEmpleado->setDias(json_encode($dias));
    $HorarioEmpleado->setEmpleadoId($empleado);
    $em->persist($HorarioEmpleado);
    $em->flush();

    return new Response("Horario guardado exitosamente");

  }
  
  
 

  public function compras(Request $request){
      return $this->render('clientes/compras.html.twig'); 
  }


  private function parse_xml_cfid($ruta_xml){


  $xml = simplexml_load_file($ruta_xml); 
  $ns = $xml->getNamespaces(true);
  $xml->registerXPathNamespace('c', $ns['cfdi']);
  $xml->registerXPathNamespace('t', $ns['tfd']);
 

  //EMPIEZO A LEER LA INFORMACION DEL CFDI E IMPRIMIRLA 
  $xml_json=array();
  foreach ($xml->xpath('//cfdi:Comprobante') as $key=>$cfdiComprobante){ 

      $xml_json['Comprobante']['Version']=(String)$cfdiComprobante['Version']; 
      $xml_json['Comprobante']['Fecha']=(String)$cfdiComprobante['Fecha'];
      $xml_json['Comprobante']['Sello']=(String)$cfdiComprobante['Sello'];
      $xml_json['Comprobante']['Total']=(String)$cfdiComprobante['Total'];
      
      if(isset($cfdiComprobante['Descuento'])){
        $xml_json['Comprobante']['Descuento']=(String)$cfdiComprobante['Descuento'];
      }else{
        $xml_json['Comprobante']['Descuento']=0;
      }
      
      $xml_json['Comprobante']['SubTotal']=(String)$cfdiComprobante['SubTotal'];
      $xml_json['Comprobante']['Certificado']=(String)$cfdiComprobante['Certificado'];
      $xml_json['Comprobante']['FormaDePago']=(String)$cfdiComprobante['FormaPago'];
      $xml_json['Comprobante']['NoCertificado']=(String)$cfdiComprobante['NoCertificado'];
      $xml_json['Comprobante']['TipoDeComprobante']=(String)$cfdiComprobante['TipoDeComprobante'];
      $xml_json['Comprobante']['Folio']=(String)$cfdiComprobante['Folio'];


  }
  

  foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor){ 

   $xml_json['Emisor']['Rfc']=(String)$Emisor['Rfc'];
   $xml_json['Emisor']['Nombre']=(String)$Emisor['Nombre'];

  }
  
  

  foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:DomicilioFiscal') as $DomicilioFiscal){ 
  
   $xml_json['DomicilioFiscal']['Pais']=(String)$DomicilioFiscal['Pais'];
   $xml_json['DomicilioFiscal']['Calle']=(String)$DomicilioFiscal['Calle'];
   $xml_json['DomicilioFiscal']['Estado']=(String)$DomicilioFiscal['Estado'];
   $xml_json['DomicilioFiscal']['Colonia']=(String)$DomicilioFiscal['Colonia'];
   $xml_json['DomicilioFiscal']['Municipio']=(String)$DomicilioFiscal['Municipio'];
   $xml_json['DomicilioFiscal']['NoExterior']=(String)$DomicilioFiscal['NoExterior'];
   $xml_json['DomicilioFiscal']['CodigoPostal']=(String)$DomicilioFiscal['CodigoPostal'];
  
 
  }

  foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor//cfdi:ExpedidoEn') as $ExpedidoEn){ 
   $xml_json['ExpedidoEn']['Pais']=(String)$DomicilioFiscal['Pais'];
   $xml_json['ExpedidoEn']['Calle']=(String)$DomicilioFiscal['Calle'];
   $xml_json['ExpedidoEn']['Estado']=(String)$DomicilioFiscal['Estado'];
   $xml_json['ExpedidoEn']['Colonia']=(String)$DomicilioFiscal['Colonia'];
   $xml_json['ExpedidoEn']['Municipio']=(String)$DomicilioFiscal['Municipio'];
   $xml_json['ExpedidoEn']['NoExterior']=(String)$DomicilioFiscal['NoExterior'];
   $xml_json['ExpedidoEn']['codigoPostal']=(String)$DomicilioFiscal['codigoPostal'];
  
}    

foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor){ 
   
   $xml_json['Receptor']['Rfc']=(String)$Receptor['Rfc'];
   $xml_json['Receptor']['Nombre']=(String)$Receptor['Nombre'];
} 


foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor//cfdi:Domicilio') as $ReceptorDomicilio){ 
 
   $xml_json['ReceptorDomicilio']['Pais']=(String)$ReceptorDomicilio['Pais'];
   $xml_json['ReceptorDomicilio']['Calle']=(String)$ReceptorDomicilio['Calle'];
   $xml_json['ReceptorDomicilio']['Estado']=(String)$ReceptorDomicilio['Estado'];
   $xml_json['ReceptorDomicilio']['Colonia']=(String)$ReceptorDomicilio['Colonia'];
   $xml_json['ReceptorDomicilio']['Municipio']=(String)$ReceptorDomicilio['Municipio'];
   $xml_json['ReceptorDomicilio']['NoExterior']=(String)$ReceptorDomicilio['NoExterior'];
   $xml_json['ReceptorDomicilio']['CodigoPostal']=(String)$ReceptorDomicilio['CodigoPostal'];
} 
foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){ 
   
    $xml_json['Concepto']['Unidad']=(String)$Concepto['Unidad'];
    $xml_json['Concepto']['Base']=(String)$Concepto['Base'];
    $xml_json['Concepto']['Importe']=(String)$Concepto['Importe'];
    $xml_json['Concepto']['Cantidad']=(String)$Concepto['Cantidad'];
    $xml_json['Concepto']['Descripcion']=(String)$Concepto['Descripcion'];
    $xml_json['Concepto']['ValorUnitario']=(String)$Concepto['ValorUnitario'];
    
} 

foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Traslados//cfdi:Traslado') as $Traslado){ 
   
  $xml_json['Traslado']['Tasa']=(String)$Traslado['Tasa'];
  $xml_json['Traslado']['Importe']=(String)$Traslado['Importe'];
  $xml_json['Traslado']['Impuesto']=(String)$Traslado['Impuesto'];
  $xml_json['Traslado']['TasaCuota']=(String)$Traslado['TasaCuota'];
  $xml_json['Traslado']['TipoFactor']=(String)$Traslado['TipoFactor'];

}

foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {

   $xml_json['TimbreFiscalDigital']['SelloCFD']=(String)$tfd['SelloCFD'];
   $xml_json['TimbreFiscalDigital']['FechaTimbrado']=(String)$tfd['FechaTimbrado'];
   $xml_json['TimbreFiscalDigital']['UUID']=(String)$tfd['UUID'];
   $xml_json['TimbreFiscalDigital']['NoCertificadoSAT']=(String)$tfd['NoCertificadoSAT'];
   $xml_json['TimbreFiscalDigital']['Version']=(String)$tfd['Version'];
   $xml_json['TimbreFiscalDigital']['SelloSAT']=(String)$tfd['SelloSAT'];
 
}

$json= json_encode($xml_json);  


return $json;

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



function get_dates_week($year = 0, $week = 0)
{
    // Se crea objeto DateTime del 1/enero del año ingresado
    $fecha = \DateTime::createFromFormat('Y-m-d', $year . '-1-01');
    $w = $fecha->format('W'); // Número de la semana
    // Se agrega semanas hasta igualar
   
    while ($week >= $w) { 
        if((int)$w==(int)$week){
          break;
        }
        $fecha=($fecha->modify('+1 week'));
        //$fecha->add(DateInterval::createfromdatestring('+1 week'));
        $w = $fecha->format('W');
        
    }

    // Ahora $fecha pertenece a la semana buscada
    // Se debe obtener el primer y el último día

    // Si $fecha no es el primer día de la semana, se restan días
    if ($fecha->format('N') > 1) {
        $format = '-' . ($fecha->format('N') - 1) . ' day';
        $fecha->modify($format);
      
       // $fecha->add(\DateInterval::createfromdatestring($format));
    }
    // Ahora $fecha es el primer día de esa semana

    // Se clona la fecha en $fecha2 y se le agrega 6 días
    $fecha2 = clone($fecha);
    $fecha2->modify('+6 day');

    // Devuelve un array con ambas fechas
    return [$fecha, $fecha2];   
}

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/nomina/guardar/incapacidad", name="clientes_nomina_guardar_incapacidad", methods={"POST"})
     */
    public function nomina_guardar_incapacidad(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $semana = $request->request->get('incapacidad-semana');
        $anio = $request->request->get('incapacidad-anio');
        $dia = $request->request->get('incapacidad-dia');
        $diaPos = $request->request->get('incapacidad-dia-posicion');
        $mes = (new \DateTime())->setISODate($anio, $semana)->format('m');
        $cliente = $request->request->get('incapacidad-cliente');
        $empleado = $request->request->get('incapacidad-empleado');

        $clienteObj = $em->getRepository(Clientes::class)->findOneById($cliente);
        $empleadoObj = $em->getRepository(Empleados::class)->findOneById($empleado);

        $incapacidad = $request->files->get('incapacidad-file');

        if($incapacidad instanceof UploadedFile) {

            $cliente_path = str_replace(" ","-", $clienteObj->getRazonSocial());
            $empleado_path = str_replace(" ","-", $empleadoObj->getNombres());

                try {

                    $horario = $em->getRepository(HorarioEmpleado::class)->findOneBy([
                        'empleadoId' => $empleadoObj->getId(),
                        'year' => $anio,
                        'semana' => $semana
                    ], ['id' => 'DESC']);

                    //Si no existe horario se crea
                    if(null == $horario){

                        $horarioDias = [
                            'L' => 'A',
                            'M' => 'A',
                            'MI' => 'A',
                            'J' => 'A',
                            'V' => 'A',
                            'S' => 'A',
                            'D' => 'A',
                        ];

                        $horarioDias[$diaPos] = 'I';

                        $horario = new HorarioEmpleado();
                        $horario->setEmpleadoId($empleadoObj->getId());
                        $horario->setYear($anio);
                        $horario->setSemana($semana);
                        $horario->setDias(json_encode($horarioDias));
                    } else {
                        $dias = json_decode($horario->getDias());
                        $dias->{$diaPos} = 'I';

                        $horario->setDias(json_encode($dias));

                    }

                    $em->persist($horario);
                    $em->flush();


                    $incapacidad->move(
                        'uploads' . DIRECTORY_SEPARATOR .
                        $cliente_path . DIRECTORY_SEPARATOR .
                        'nomina' . DIRECTORY_SEPARATOR .
                        $empleado_path . DIRECTORY_SEPARATOR .
                        'incapacidad' . DIRECTORY_SEPARATOR .
                        $anio . DIRECTORY_SEPARATOR .
                        $mes . DIRECTORY_SEPARATOR.
                        $dia,
                        $incapacidad->getClientOriginalName()
                    );

                } catch (FileException $e) {
                   // dd($e->getMessage());
                }
        }

        return $this->redirectToRoute('clientes_nomina_list',array('clienteid'=>$clienteObj->getId()));

    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/nomina/dar/baja", name="clientes_nomina_dar_baja", methods={"POST"})
     */
    public function nomina_dar_baja(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $cliente = $request->request->get('clienteid');
        $empleado = $request->request->get('empleadoid');

        $clienteObj = $em->getRepository(Clientes::class)->findOneById($cliente);
        $empleadoObj = $em->getRepository(Empleados::class)->findOneById($empleado);

        $bajafile = $request->files->get('baja-file');

        if($bajafile instanceof UploadedFile) {

            $cliente_path = str_replace(" ","-", $clienteObj->getRazonSocial());
            $empleado_path = str_replace(" ","-", $empleadoObj->getNombres());

                try {

                    $empleadoObj->setFechaBaja(new \DateTime('now'));
                    $empleadoObj->setEstado(0);
                    $ruta='uploads/'.$cliente_path.'/nomina/'.$empleado_path.'/baja/'.$bajafile->getClientOriginalName();
                    
                    $bajafile->move(
                        'uploads' . DIRECTORY_SEPARATOR .
                        $cliente_path . DIRECTORY_SEPARATOR .
                        'nomina' . DIRECTORY_SEPARATOR .
                        $empleado_path . DIRECTORY_SEPARATOR .
                        'baja' . DIRECTORY_SEPARATOR .
                        $bajafile->getClientOriginalName()
                    );

                    $empleadoObj->setFileBaja($ruta);
                    $em->persist($empleadoObj);
                    $em->flush();

                } catch (FileException $e) {
                   // dd($e->getMessage());
                }
        }

        return $this->redirectToRoute('clientes_nomina_list',array('clienteid'=>$clienteObj->getId()));

    }
    /**
     * Get days in a week
     * @param $year
     * @param $week
     * @return array
     * @throws \Exception
     */
    private function getDaysInWeek($year, $week)
    {
        $result = array();
        $datetime = new \DateTime('00:00:00');
        $datetime->setISODate($year, $week);
        $interval = new \DateInterval('P1D');
        $week = new \DatePeriod($datetime, $interval, 6);

        foreach($week as $day){
            $result[] = $day;
        }
        return $result;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/nomina/guardar/general", name="clientes_nomina_guardar_general", methods={"POST"})
     */
    public function nomina_guardar_general(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $year =  $request->request->get('nomina-year');
        $week =  $request->request->get('nomina-week');
        $cliente =  $request->request->get('nomina-cliente');

        $horarioEmpleados = $request->request->get('nomina');

        foreach ($horarioEmpleados as $empleadoId => $dias) {
            $horarioNomina = new HorarioEmpleado();
            $horarioNomina->setEmpleadoId($empleadoId);
            $horarioNomina->setYear($year);
            $horarioNomina->setSemana($week);
            $horarioNomina->setConfirmado(true);
            $horarioNomina->setDias(str_replace("'", "", json_encode($dias)));

            $em->persist($horarioNomina);
            $em->flush();

        }

        return $this->redirectToRoute('clientes_nomina_list', [
            'week' => $week,
            'year' => $year,
            'clienteid'=>$cliente,
            'exportar' => null
            ]
        );

    }


}


