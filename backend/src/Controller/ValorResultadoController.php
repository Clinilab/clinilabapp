<?php

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\ValorResultado;
use App\Entity\OrdenEstudio;
use App\Entity\ResultadoEstudio;
use App\Form\ValorResultadoType;
use App\Entity\EstadoEstudio;
use App\Entity\Estudio;
use App\Entity\User;
use App\Entity\Orden;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

/**
 * ValorResultado controller.
 * @Route("/api/valor/resultado", name="api_")
 */
class ValorResultadoController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/{idResultadoEstudio}")
   *
   * @return Response
   */
  public function getValorResultadoAction($idResultadoEstudio)
  {
    $repository = $this->getDoctrine()->getRepository(ValorResultado::class);
    $registros = $repository->findBy(
      array('activo' => true, 'orderesultadoEstudion' => $idResultadoEstudio)
    );
    $response = array(
      'status' => 'success',
      'code' => '400',
      'msg' => 'Listado de registros',
      'data' => $registros
    );
    return $this->handleView($this->view($response));
  }

  /**
   * Create ValorResultado.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postValorResultadoAction(Request $request)
  {

    $data = json_decode($request->getContent(), true);

    $em = $this->getDoctrine()->getManager();

    foreach ($data as $key => $registro) {
      $valorResultado = new ValorResultado();
      $form = $this->createForm(ValorResultadoType::class, $valorResultado);

      $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
      $ordenEstudio = $repository->find($registro['ordenEstudio']);

      $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
      $resultadoEstudio = $repository->find($registro['id']);

      $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);
      $valorResultadoBd = $valorResultadoRepository->findOneBy(
        array(
          'activo' => true,
          'resultadoEstudio' => $resultadoEstudio->getId(),
          'ordenEstudio' => $ordenEstudio->getId()
        )
      );

      if ($valorResultadoBd) {
        $em->remove($valorResultadoBd);
        $em->flush();
      }

      $valorResultado->setResultadoEstudio($resultadoEstudio);
      $valorResultado->setOrdenEstudio($ordenEstudio);
      $valorResultado->setFechaModificacion(new \DateTime('now'));
      $valorResultado->setActivo(true);

      $estadoEstudioRepository = $this->getDoctrine()->getRepository(EstadoEstudio::class);
      $ordenEstudio->setEstadoEstudio($estadoEstudioRepository->find(2));

      $userRepository = $this->getDoctrine()->getRepository(User::class);
      $ordenEstudio->setUser($userRepository->find($registro['user']));
      $ordenEstudio->setFechaValidacion(new \DateTime('now'));

      $ordenRepository = $this->getDoctrine()->getRepository(Orden::class);
      $orden = $ordenRepository->find($ordenEstudio->getOrden()->getId());
      $fechaFin = new \DateTime('now');
      $orden->setFechaFin($fechaFin);

      $em->persist($ordenEstudio);
      $em->flush();

      if ($registro['tipo'] == 'formula') {
        foreach ($data as $key => $registroFormula) {
          $registro['formula'] = str_replace($registroFormula['nombre'], $registroFormula['valor'], $registro['formula']);
        }

        $pos = strpos($registro['formula'], '^');

        if ($pos) {
          $arrayFormula = explode("^", $registro['formula']);
          $fun = '$var = ' . $arrayFormula[0] . ';';
          eval($fun);

          $var = $this->get('app.operations')->truncate(
            pow($var, $arrayFormula[1]),
            2
          );

          //$valorResultado->setValor(pow($var, $arrayFormula[1]));
          $valorResultado->setValor($var);
        } else {
          $fun = '$var = ' . $registro['formula'] . ';';
          eval($fun);

          $var = $this->get('app.operations')->truncate(
            $var,
            2
          );

          $valorResultado->setValor($var);
        }
      } else {
        if ($registro['tipo'] == 'title') {
          $valorResultado->setValor($registro['nombre']);
        } else {
          $valorResultado->setValor($registro['valor']);
        }
      }

      $em->persist($valorResultado);
      $em->flush();
    }

    $response = array(
      'status' => 'success',
      'code' => '400',
      'msg' => 'Registro creado'
    );

    return $this->handleView($this->view($response, Response::HTTP_CREATED));
  }

  /**
   * Create ValorResultado.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditValorResultadoAction(Request $request, ValorResultado $valorResultado)
  {
    $form = $this->createForm(ValorResultadoType::class, $valorResultado);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($valorResultado);
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
   * Show ValorResultado.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowValorResultadoAction(ValorResultado $registro)
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
  public function getDeleteValorResultadoAction(ValorResultado $valorResultado)
  {
    $em = $this->getDoctrine()->getManager();
    if ($valorResultado->getActivo()) {
      $valorResultado->setActivo(false);
    } else {
      $valorResultado->setActivo(true);
    }

    $em->flush();

    $response = array(
      'status' => 'success',
      'code' => '400',
      'msg' => 'Registro eliminado',
      'data' => $valorResultado
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getValorResultadoSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(ValorResultado::class);
    $registros = $repository->findByActivo(true);
    $registrosArray = null;
    foreach ($registros as $key => $r) {
      $registrosArray[$key] = array(
        'id' => $r->getId(),
        'text' => $r->getCodigo() . '/' . $r->getNombre(),
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
   * @Rest\Post("/find/by/fecha")
   *
   * @return Response
   */
  public function postFindByFechatAction(Request $request)
  {
    $repository = $this->getDoctrine()->getRepository(Orden::class);

    $data = json_decode($request->getContent(), true);

    $ordenes = $repository->findByFecha($data['fechaInicio'], $data['fechaFin']);


    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('User List');

    $sheet->getCell('A1')->setValue('Fecha');
    $sheet->getCell('B1')->setValue('Paciente');
    $sheet->getCell('C1')->setValue('Tipo Identificacion');
    $sheet->getCell('D1')->setValue('Identificacion');
    $sheet->getCell('E1')->setValue('Edad');
    $sheet->getCell('F1')->setValue('Genero');
    $sheet->getCell('G1')->setValue('Direccion');
    $sheet->getCell('H1')->setValue('Telefono');
    $sheet->getCell('I1')->setValue('EAPB');
    $sheet->getCell('J1')->setValue('Estudio');
    $sheet->getCell('K1')->setValue('Variable');
    $sheet->getCell('L1')->setValue('Resultado');
    $sheet->getCell('M1')->setValue('Profecional');

    // Increase row cursor after header write
    $list = [];
    foreach ($ordenes as $orden) {
      $anos = $this->busca_edad($orden->getPaciente()->getFechaNacimiento()->format('Y-m-d'));
      $repositoryOrdenEstudio = $this->getDoctrine()->getRepository(OrdenEstudio::class);

      $ordenesEstudios = $repositoryOrdenEstudio->findByOrden($orden->getId());

      foreach ($ordenesEstudios as $key => $ordenEstudio) {
        $repositoryValorResultado = $this->getDoctrine()->getRepository(ValorResultado::class);
        $valoresResultados = $repositoryValorResultado->findByOrdenEstudio($ordenEstudio->getId());

        foreach ($valoresResultados as $key => $valorResultado) {
          $list[] = [
            $orden->getFecha()->format('d/m/Y'),
            $orden->getPaciente()->getUser()->getNombres() . $orden->getPaciente()->getUser()->getApellidos(),
            $orden->getPaciente()->getUser()->getTipoIdentificacion()->getNombre(),
            $orden->getPaciente()->getUser()->getIdentificacion(),
            $anos,
            $orden->getPaciente()->getUser()->getGenero(),
            $orden->getPaciente()->getDireccion(),
            $orden->getPaciente()->getUser()->getTelefono(),
            $orden->getEapb()->getNombre(),

            $valorResultado->getOrdenEstudio()->getEstudio()->getNombre(),
            $valorResultado->getResultadoEstudio()->getNombre(),

            $valorResultado->getValor(),
            $valorResultado->getOrdenEstudio()->getUser()->getNombres() . $valorResultado->getOrdenEstudio()->getUser()->getApellidos(),

          ];
        }
      }
    }

    $sheet->fromArray($list, null, 'A2', true);


    $writer = new Xlsx($spreadsheet);

    $writer->save('Reporte.xlsx');

    $response = array(
      'status' => 'success',
      'code' => '200',
      'msg' => 'Listado de registros',
      'data' => 'Reporte.xlsx'
    );
    return $this->handleView($this->view($response));
  }

  function busca_edad($fecha_nacimiento)
  {
    $dia = date("d");
    $mes = date("m");
    $ano = date("Y");


    $dianaz = date("d", strtotime($fecha_nacimiento));
    $mesnaz = date("m", strtotime($fecha_nacimiento));
    $anonaz = date("Y", strtotime($fecha_nacimiento));


    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual

    if (($mesnaz == $mes) && ($dianaz > $dia)) {
      $ano = ($ano - 1);
    }

    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual

    if ($mesnaz > $mes) {
      $ano = ($ano - 1);
    }

    //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad

    $edad = ($ano - $anonaz);


    return $edad;
  }

  /**
   * List by  ValorResultado.
   * @Rest\Post("/list/chart")
   *
   * @return Response
   */
  public function listByChartAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);

    $em = $this->getDoctrine()->getManager();

    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
    $ordenEstudio = $repository->findOneById($data['idOrdenEstudio']);

    //return $this->handleView($this->view($data, Response::HTTP_CREATED));

    if ($ordenEstudio) {
      $idResultadoEstudio = $data['valorEstudio']['resultado_estudio']['id'];
      $idPaciente = $ordenEstudio->getOrden()->getPaciente()->getId();

      $repository = $this->getDoctrine()->getRepository(ValorResultado::class);
      $resultados = $repository->findForChart($idResultadoEstudio, $idPaciente);

      if ($resultados) {
        $response = array(
          'status' => 'success',
          'code' => '200',
          'message' => count($resultados) . ' resultados encontrados.',
          'data' => $resultados
        );
      } else {
        $response = array(
          'status' => 'warning',
          'code' => '400',
          'message' => 'Ningún resultado encontrado.',
        );
      }
    } else {
      $response = array(
        'status' => 'warning',
        'code' => '400',
        'message' => 'No se pudo encontrar la orden para este estudio.',
      );
    }

    return $this->handleView($this->view($response, Response::HTTP_CREATED));
  }


  //Interface endpoint
  /**
   * List by  ValorResultado.
   * @Rest\Post("/interface")
   *
   * @return Response
   */
  public function interfaceAction(Request $request)
  {
      $data = json_decode($request->getContent(), true);

      $em = $this->getDoctrine()->getManager();

      $repositoryOrden = $this->getDoctrine()->getRepository(Orden::class);
      //$orden = $repositoryOrden->findOneByNumero($data['numero_orden']);
      $orden = $repositoryOrden->findOneBy(['id_con_maquina' => $data['numero_orden']]);

      $repositoryEstudio = $this->getDoctrine()->getRepository(Estudio::class);
      $estudio = $repositoryEstudio->findOneByCodigo($data['codigo_examen']);

      if ($orden && $estudio) {
        $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
        $ordenEstudio = $repository->findOneBy(
          array(
            'orden' => $orden->getId(),
            'estudio' => $estudio->getId(),
            'estadoEstudio' => 1,
            'activo' => 1
          )
        );

        if ($ordenEstudio) {
          if ($ordenEstudio->getEstadoEstudio()->getId() == 1) {
            $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);

            foreach ($data['results'] as $key => $registro) {
              $valorResultado = new ValorResultado();

              $resultadoEstudio = $repository->findOneBy(
                array(
                  'estudio' => $estudio->getId(),
                  'variableMaquina' => $registro['id'],
                  'activo' => 1
                )
              );

              if (!$resultadoEstudio) {
                $response = array(
                  'status' => 'warning',
                  'code' => '204',
                  'message' => 'Variable no encontrada en el LIS: ' . $registro['id'],
                );
                return $this->handleView($this->view($response, Response::HTTP_CREATED));
              }

              $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);
              $valorResultadoBd = $valorResultadoRepository->findOneBy(
                array(
                  'activo' => true,
                  'resultadoEstudio' => $resultadoEstudio->getId(),
                  'ordenEstudio' => $ordenEstudio->getId()
                )
              );

              if ($valorResultadoBd) {
                $em->remove($valorResultadoBd);
                $em->flush();
              }

              $valorResultado->setResultadoEstudio($resultadoEstudio);
              $valorResultado->setOrdenEstudio($ordenEstudio);
              $valorResultado->setFechaModificacion(new \DateTime('now'));
              $valorResultado->setValor($registro['value']);
              $valorResultado->setActivo(true);

              $estadoEstudioRepository = $this->getDoctrine()->getRepository(EstadoEstudio::class);
              $ordenEstudio->setEstadoEstudio($estadoEstudioRepository->find(1));

              //$userRepository = $this->getDoctrine()->getRepository(User::class);
              //$ordenEstudio->setUser($userRepository->find($registro['user']));
              // $ordenEstudio->setFechaValidacion(new \DateTime('now'));

              $fechaFin = new \DateTime('now');
              $orden->setFechaFin($fechaFin);

              $em->persist($valorResultado);
              $em->flush();
            }

            $response = array(
              'status' => 'success',
              'code' => '200',
              'message' => count($data['results']) . ' valores analizados.'
            );

            return $this->handleView($this->view($response, Response::HTTP_CREATED));
          } else {
            $response = array(
              'status' => 'warning',
              'code' => '204',
              'message' => 'Este estudio ya fue validado por lo tanto no se puede actualizar nuevamente.',
            );
          }
        } else {
          $response = array(
            'status' => 'warning',
            'code' => '204',
            'message' => 'No se pudo encontrar la orden para este estudio y/o ya fue validada',
          );
        }
      } else {
        $response = array(
          'status' => 'warning',
          'code' => '400',
          'message' => 'No se pudo encontrar la orden y/o el estudio TTT',
        );
    }

    return $this->handleView($this->view($response));
  }






  /** 
   * 
   * @Rest\Post("/find/by/reportefecha")
   *
   * @return Response
   */

  public function postReporteFecha(Request $request)
  {
    $data_response = json_decode($request->getContent(), true);
    $conn = $this->getDoctrine()->getConnection();

    try {
      $sql = 'SELECT o.fecha AS fecha_orden, o.numero AS numero_orden, ti.codigo AS tipo_ide, f.identificacion,
              CONCAT(f.nombres, " ", f.apellidos) AS nombre_paciente,
              TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) AS Edad,
              f.genero, ep.nombre AS nom_eapb, et.codigo AS cups, et.nombre AS n_cups,
              re.nombre AS variable, vr.valor
              FROM valor_resultado vr 
              INNER JOIN resultado_estudio re ON re.id = vr.resultado_estudio_id
              INNER JOIN estudio et ON et.id = re.estudio_id
              INNER JOIN orden_estudio oe ON oe.id = vr.orden_estudio_id
              INNER JOIN orden o ON o.id = oe.orden_id
              INNER JOIN paciente AS e ON e.id = o.paciente_id
              INNER JOIN fos_user AS f ON f.id = e.user_id
              INNER JOIN servicio AS g ON g.id = o.servicio_id
              INNER JOIN tipo_identificacion AS ti ON ti.id = f.tipo_identificacion_id
              INNER JOIN eapb AS ep ON ep.id = o.eapb_id
              WHERE oe.estado_estudio_id = 2 AND oe.activo = 1 AND o.fecha >= :fecha_ini 
              AND o.fecha <= :fecha_fin';


      $stmt = $conn->prepare($sql);
      $resultSet = $stmt->executeQuery(array('fecha_ini' => $data_response['fechaInicio'], 'fecha_fin' => $data_response['fechaFin']));
      $ordenes = $resultSet->fetchAllAssociative();

      $csvData = "Fecha_orden,Numero_orden,Tipo_identificacion,Identificacion,Nombre_paciente,Edad,Genero,Eapb,Cups,Nombre_codigo,Variable,Valor\n";

      foreach ($ordenes as $orden) {
        $csvData .= implode(",", $orden) . "\n";
      }


      $filePath = './Reporte.csv';
      file_put_contents($filePath, $csvData);


      $response = array(
        'status' => 'sucess',
        'code' => '200',
        'msg' => 'Informe generado',
        'data' =>  'sucess'
      );

      return $this->handleView($this->view($response));
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




  /** 
   * 
   * @Rest\Post("/find/by/reporteTodosEstudios")
   *
   * @return Response
   */

  public function postReporteEstudios(Request $request)
  {
    $data_response = json_decode($request->getContent(), true);
    $conn = $this->getDoctrine()->getConnection();

    try {

      $sql = 'SELECT o.fecha AS fecha_orden,oe.fecha_validacion as Fecha_validacion, o.numero AS numero_orden, ti.codigo AS tipo_ide, f.identificacion,
      CONCAT(f.nombres, " ", f.apellidos) AS nombre_paciente,
      TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) AS Edad,
      f.genero, ep.nombre AS nom_eapb, et.codigo AS cups, et.nombre AS n_cups,
      re.nombre AS variable, vr.valor, "Con Resultado"
      FROM valor_resultado vr 
      INNER JOIN resultado_estudio re ON re.id = vr.resultado_estudio_id
      INNER JOIN estudio et ON et.id = re.estudio_id
      INNER JOIN orden_estudio oe ON oe.id = vr.orden_estudio_id
      INNER JOIN orden o ON o.id = oe.orden_id
      INNER JOIN paciente AS e ON e.id = o.paciente_id
      INNER JOIN fos_user AS f ON f.id = e.user_id
      INNER JOIN servicio AS g ON g.id = o.servicio_id
      INNER JOIN tipo_identificacion AS ti ON ti.id = f.tipo_identificacion_id
      INNER JOIN eapb AS ep ON ep.id = o.eapb_id
      WHERE oe.estado_estudio_id = 2 and  o.fecha>=:fecha_ini  and o.fecha<=:fecha_fin
      and oe.activo =1 and vr.valor <> ""             
      union all
      select od.fecha AS fecha_orden,os.fecha_validacion as Fecha_validacion , od.numero AS numero_orden,
      ti.codigo AS tipo_ide, f.identificacion, CONCAT(f.nombres, " ", f.apellidos) AS nombre_paciente,
      TIMESTAMPDIFF(YEAR, e.fecha_nacimiento, CURDATE()) AS Edad, f.genero,
      ep.nombre AS nom_eapb, et.codigo AS cups, et.nombre AS n_cups, "Sin Variables" as variables, "Sin Valor" as valor_resultado, "Sin resultado" 
      from orden as  od
      inner join orden_estudio as os on (od.id = os.orden_id)
      INNER JOIN estudio et ON et.id = os.estudio_id
      INNER JOIN paciente AS e ON e.id = od.paciente_id
      INNER JOIN fos_user AS f ON f.id = e.user_id 
      INNER JOIN tipo_identificacion AS ti ON ti.id = f.tipo_identificacion_id
      INNER JOIN eapb AS ep ON ep.id = od.eapb_id
      where os.estado_estudio_id = 1 and os.estado_estudio_id = 1 and os.activo = 1
      and  od.fecha>=:fecha_ini and od.fecha<=:fecha_ini';



      $stmt = $conn->prepare($sql);
      $resultSet = $stmt->executeQuery(array('fecha_ini' => $data_response['fechaInicio'], 'fecha_fin' => $data_response['fechaFin']));
      $ordenes = $resultSet->fetchAllAssociative();

      $csvData = "fecha_orden,Fecha_validacion,numero_orden,tipo_ide,identificacion,nombre_paciente,Edad,genero,nom_eapb,cups,n_cups,variable,valor,Con_Resultado\n";

      foreach ($ordenes as $orden) {
        $csvData .= implode(",", $orden) . "\n";
      }


      $filePath = './Reporte.csv';
      file_put_contents($filePath, $csvData);


      $response = array(
        'status' => 'sucess',
        'code' => '200',
        'msg' => 'Informe generado',
        'data' =>  'sucess'
      );

      return $this->handleView($this->view($response));
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


  /*   public function postReporteFecha(Request $request)
  {
        $data_response = json_decode($request->getContent(), true);    
        $conn = $this->getDoctrine()->getConnection();

        
      try{
      
               $sql  ='select  o.fecha as fecha_orden,o.numero as numero_orden,ti.codigo as tipo_ide ,f.identificacion,
                 concat(f.nombres," ",f.apellidos) as nombre_paciente,
                TIMESTAMPDIFF(YEAR,e.fecha_nacimiento, CURDATE()) AS Edad,
                f.genero,ep.nombre as nom_eapb, et.codigo as cups,et.nombre as n_cups ,re.nombre as variable,vr.valor  from valor_resultado vr 
                inner join resultado_estudio re on (re.id = vr.resultado_estudio_id)
                inner join estudio et on (et.id=re.estudio_id)
                inner join orden_estudio oe  on (oe.id = vr.orden_estudio_id)
                inner join orden o  on (o.id = oe.orden_id)
                inner join paciente as e on(e.id = o.paciente_id) 
                inner join fos_user as f on(f.id = e.user_id)
                inner join servicio as g on(g.id = o.servicio_id)
                inner join tipo_identificacion  as ti  on(ti.id = f.tipo_identificacion_id )
                inner join eapb as ep on(ep.id =  o.eapb_id)
                where oe.estado_estudio_id  = 2 and oe.activo=1 and o.fecha >= :fecha_ini 
                and o.fecha <=:fecha_fin';
                $stmt = $conn->prepare($sql);
                $resultSet = $stmt->executeQuery(array('fecha_ini'=> $data_response['fechaInicio'],'fecha_fin'=> $data_response['fechaFin']));
                //$resultSet = $stmt->executeQuery();
        
                // returns an array of arrays (i.e. a raw data set)
                $ordenes =  $resultSet->fetchAllAssociative();
              

                $spreadsheet = new Spreadsheet();

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle('User List');
                $sheet->getCell('A1')->setValue('Fecha_orden');
                $sheet->getCell('B1')->setValue('Numero_orden');
                $sheet->getCell('C1')->setValue('Tipo_identificacion');
                $sheet->getCell('D1')->setValue('Identificacion');
                $sheet->getCell('E1')->setValue('Nombre_paciente');
                $sheet->getCell('F1')->setValue('Edad');
                $sheet->getCell('G1')->setValue('Genero');
                $sheet->getCell('H1')->setValue('Eapb');
                $sheet->getCell('I1')->setValue('Cups');
                $sheet->getCell('J1')->setValue('Nombre_codigo');
                $sheet->getCell('K1')->setValue('Variable');
                $sheet->getCell('L1')->setValue('Valor');                

    // Increase row cursor after header write
          $list = [];
          foreach ($ordenes as  $key => $orden) {
            
              $list[] = [            
              $orden['fecha_orden'],
              $orden['numero_orden'],
              $orden['tipo_ide'],
              $orden['identificacion'],
              $orden['nombre_paciente'],              
              $orden['Edad'],
              $orden['genero'],
              $orden['nom_eapb'],
              $orden['cups'],
              $orden['n_cups'],
              $orden['variable'],
              $orden['valor'],

          ];     

    }

    $sheet->fromArray($list,null, 'A2', true);
 

    $writer = new Xlsx($spreadsheet);

    $writer->save('Reporte.xlsx');

    $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Listado de registros', 
        'data' =>  'ok'
    );
    return $this->handleView($this->view($response));
  }

   catch (\Exception $e)
   {


    $response = array(
      'status' => 'false', 
      'code' => '200', 
      'msg' => 'Listado de registros', 
      'data' =>  $e->getMessage()
  );
  return $this->handleView($this->view($response));

   }

  }*/
}
