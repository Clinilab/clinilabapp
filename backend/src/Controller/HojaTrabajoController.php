<?php

namespace App\Controller;

use App\Entity\HojaTrabajo;
use App\Form\HojaTrabajoType;
use App\Repository\HojaTrabajoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Orden;
use App\Entity\OrdenEstudio;
use App\Entity\Estudio;
use App\Entity\ResultadoEstudio ;
use App\Entity\ValorResultado;
use App\Entity\Consecutivo;
use App\Entity\Eapb;
use App\Entity\Institucion;
use App\Entity\Area;
/**
 * HojaTrabajo controller.
 * @Route("/api/hojaTrabajo", name="api_")
 */
class HojaTrabajoController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getHojaTrabajoAction()
  {
    $repository = $this->getDoctrine()->getRepository(HojaTrabajo::class);
    $registros = $repository->findByActivo(true);

    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Listado de registros', 
        'data' => $registros
    );
    return $this->handleView($this->view($response));
  }

  /**
   * Create HojaTrabajo.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postHojaTrabajoAction(Request $request)
  {
    $hojaTrabajo = new HojaTrabajo();
    $form = $this->createForm(HojaTrabajoType::class, $hojaTrabajo);
    $data = json_decode($request->get("data",null), true); 
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
        
        $em = $this->getDoctrine()->getManager();

        $fileHeader = $request->files->get('file');
    
        if ($fileHeader) {

        $extension = $fileHeader->guessExtension(); 
        $filename = "hoja_trabajo_".$data['nombre'].'.'.$extension;
        $dir=__DIR__."/../../public/img/hojas_trabajo";

        $fileHeader->move($dir,$filename);
        $hojaTrabajo->setArchivo($filename);
        } 

        $fecha = new \DateTime('now');

        $hojaTrabajo->setFecha($fecha);
        $hojaTrabajo->setActivo(true);
        $em->persist($hojaTrabajo);
        $em->flush();
        $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'Registro creado', 
        );
      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create HojaTrabajo.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditHojaTrabajoAction(Request $request,HojaTrabajo $hojaTrabajo)
  {
    $form = $this->createForm(HojaTrabajoType::class, $hojaTrabajo);
    $data = json_decode($request->get("data",null), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $fileHeader = $request->files->get('file');
      if ($fileHeader) {
            $extension = $fileHeader->guessExtension(); 
            $filename = "hoja_trabajo_".$data['nombre'].'.'.$extension;
            $dir=__DIR__."/../../public/img/hojas_trabajo";

            $fileHeader->move($dir,$filename);
            $hojaTrabajo->setArchivo($filename);
        } 

      $em->persist($hojaTrabajo);
      $em->flush();
      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Registro creado', 
      );
      return $this->handleView($this->view($response));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

  /**
   * Show HojaTrabajo.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowHojaTrabajoAction(HojaTrabajo $registro)
  {
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro',
        'data' => $registro
    );
    return $response;
  }


  /**
   * 
   * @Rest\Get("/{id}/delete")
   *
   * @return Response
   */
  public function getDeleteHojaTrabajoAction(HojaTrabajo $hojaTrabajo)
  {
    $em = $this->getDoctrine()->getManager();
    if($hojaTrabajo->getActivo()){
      $hojaTrabajo->setActivo(false);
    }else{
      $hojaTrabajo->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $hojaTrabajo
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getHojaTrabajoSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(HojaTrabajo::class);
    $registros = $repository->findByActivo(true);
    $registrosArray= null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getNombres().'/'.$r->getIdentificacion(), 
        );
    }

    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Listado de registros', 
        'data' => $registrosArray
    );
    return $this->handleView($this->view($response));
  }
   /** 
   * 
   * @Rest\Get("/find/by/reportefecha/{area}/{ini}/{fin}/{tipo}")
   *
   * @return Response
   */
  public function getHojaTrabajo($area,$ini,$fin,$tipo)
  {
    $conn = $this->getDoctrine()->getConnection();
    try {

      //Todos los ordenes con items con resultado
      $paraEstado = "";
      $nombreEstado ="";
      if ($tipo == 1) {
          $paraEstado = 'b.estado_estudio_id = 2';
          $nombreEstado ="Todos las ordenes con resultados";
      }
      // Ordenes con item sin resultado
      if ($tipo == 2) {      
          $paraEstado = 'b.estado_estudio_id = 1';
          $nombreEstado ="Todos las ordenes sin resultado";
      }
      // Todas las ordenes
      if ($tipo == 3) {      
          $paraEstado = "b.estado_estudio_id IN (1, 2)";
          $nombreEstado ="Todos las ordenes con y sin resultados";
      }
      // ...
      
      // Se consulta todas los estudios con sus abreviaturas correspondientes
      // que este activos y de las ordenes
      $sqlEstudios = 'SELECT DISTINCT c.id, c.abrev
                      FROM orden_estudio AS b
                      INNER JOIN orden AS od ON (od.id = b.orden_id)
                      INNER JOIN estudio AS c ON (c.id = b.estudio_id) 
                      INNER JOIN area AS d ON (d.id = c.areaid)
                      WHERE b.activo<>0 and  date(od.fecha) >= :fecha_ini AND date(od.fecha) <= :fecha_fin AND d.id = :idarea
                      AND ' . $paraEstado . ' AND c.activo = 1 AND b.activo <> 0
                      ORDER BY c.id';
      
      $stEstudios = $conn->prepare($sqlEstudios);      
      $resultEstudios = $stEstudios->executeQuery(array('fecha_ini' => $ini, 'fecha_fin' => $fin, 'idarea' => $area));
      $listaEstudios = $resultEstudios->fetchAllAssociative();

      
      
      if(!$listaEstudios)
      {
        $response = array(
          'status' => 'false',
          'code' => '400',
          'msg' => 'No existen registros',
          'data' =>  null
        );
        return $this->handleView($this->view($response));
      }        

      $sql = 'select concat(f.nombres," ",f.apellidos)as nombre,f.identificacion,f.genero,
      od.fecha as fecha_orden,od.eapb_id, TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, od.fecha) as edad,od.numero,
       GROUP_CONCAT( c.id  SEPARATOR "-")  as pruebas
            from orden_estudio as  b  inner join orden as od on(od.id = b.orden_id)
            inner join estudio as c on(c.id = b.estudio_id) 
            inner join area as d on (d.id = c.areaid)
            inner join paciente as e on(e.id = od.paciente_id) 
            inner join fos_user as f on(f.id = e.user_id) 
            where b.activo<>0 and  date(od.fecha)>= :fecha_ini and date(od.fecha)<= :fecha_fin
            and c.activo = 1 and d.id = :idarea    
            AND ' . $paraEstado . ' group by e.user_id,od.id order by od.id ';
   

      $stmt = $conn->prepare($sql);
      $resultSet = $stmt->executeQuery(array('fecha_ini' => $ini, 'fecha_fin' => $fin,'idarea'=>$area));
      $ordenesHojas = $resultSet->fetchAllAssociative();     
     
      //dd($ordenesHojas);
      $entityManager = $this->getDoctrine()->getManager();        
      $areaResult = $entityManager->getRepository(Area::class)->find($area);      
      $nombreArea = $areaResult->getAreaNombre(); 


      foreach ($ordenesHojas as $registro) {
        // Separar los estudios por "|"
        $estudiosSeparados = explode('-', $registro['pruebas']);
      
        // Crear un nuevo registro con estudios separados
        $nuevoRegistro = array(
          'nombre' => $registro['nombre'],
          'genero' => substr($registro['genero'], 0, 1),
          'fecha_orden' => $registro['fecha_orden'],
          'eapb' => $registro['eapb_id'],
          'edad' => $registro['edad'],
          'numero' => $registro['numero'],
          'identificacion' => $registro['identificacion'],
          'estudios' => $estudiosSeparados
        );      
        // Agregar el nuevo registro al array de registros separados
        $ordenesImprimir[] = $nuevoRegistro;
      }      
      
      //Datos de la institucion
      $repository = $this->getDoctrine()->getRepository(Institucion::class);
      $institucion = $repository->findOneByActivo(true);

      if (!$institucion) {
        $institucion = array(
          'nombre' => 'Clinimetric',
          'identificacion' => '814000337',
          'direccion' => 'Pasto, Nariño',
          'telefono' => '7442029',
          'logo' => 'logoF.png',
        );
      } else {
        $institucion = array(
          'nombre' => $institucion->getNombre(),
          'identificacion' => $institucion->getIdentificacion(),
          'direccion' => $institucion->getDireccion(),
          'telefono' => $institucion->getTelefono(),
          'logo' => $institucion->getLogo(),
        );
      }

      $datosEncabezado = array(
        'area'  => $nombreArea,
        'desde' => $ini,
        'hasta' => $fin,
        'tipo'=> $nombreEstado
      );


      $arrayhojaTrabajo = array(
        'listaEstudios' =>$listaEstudios,
        'ordenes' => $ordenesImprimir,
        'encabezado' => $datosEncabezado
      );         
      //
      //dd( $arrayhojaTrabajo);
      $html = $this->renderView('hoja_trabajo_new/hojaTrabajoNew.html.twig', $arrayhojaTrabajo);

      $nombrePdf = $this->get('app.pdf.template.hojaTrabajoNew')->templateWorkSheet($html, array(
        'data' => $arrayhojaTrabajo,
        'institucion' => $institucion,
      ));

      $response = array(
        'status' => 'success',
        'code' => '200',
        'msg' => 'Listado de registros',
        'data' => null
      );
      //return $this->handleView($this->view($response));

    } catch (\Exception $e) {

      $response = array(
        'status' => 'false',
        'code' => '400',
        'msg' => 'Error al generar la consulta',
        'data' =>  $e->getMessage()
      );

      return $this->handleView($this->view($response));
    }
  }

  
  function busca_edad($fecha_nacimiento){
    $dia=date("d");
    $mes=date("m");
    $ano=date("Y");
    
    
    $dianaz=date("d",strtotime($fecha_nacimiento));
    $mesnaz=date("m",strtotime($fecha_nacimiento));
    $anonaz=date("Y",strtotime($fecha_nacimiento));
    
    
    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
    
    if (($mesnaz == $mes) && ($dianaz > $dia)) {
    $ano=($ano-1); }
    
    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
    
    if ($mesnaz > $mes) {
    $ano=($ano-1);}
    
     //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
    
    $edad=($ano-$anonaz);
    
    
    return $edad;
    
    
    }

  
}