<?php
namespace App\Controller;
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
use App\Form\OrdenType;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\CapabilityProfile;

/**
 * Orden controller.
 * @Route("/api/orden", name="api_")
 */
class OrdenController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getOrdenAction()
  {
    $repository = $this->getDoctrine()->getRepository(Orden::class);
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
   * Create Orden.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postOrdenAction(Request $request)
  {
    $Orden = new Orden();
    $form = $this->createForm(OrdenType::class, $Orden);
    $data = json_decode($request->getContent(), true);
    $data['fecha'] = new \DateTime($data['fecha'] . ' ' . date('H:i:s'));
    $form->submit($data);

    if ($form->isSubmitted() && $form->isValid()) {
      
      $em = $this->getDoctrine()->getManager();
      $Orden->setActivo(true);
      $eapbRepository = $this->getDoctrine()->getRepository(Eapb::class);
      $eapb = $eapbRepository->find($data['eapb']);
      $eapb->setFrecuencia($eapb->getFrecuencia() + 1);
      $em->persist($eapb);
      $em->flush();
      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();

      //$fechActual =  date('y-m-d');
     //$fechActual = '23-02-17';
      
      $fechActual =  date('y-m-d');

      //Extraigo las fechas  fechaBase y la fecha actual
      //para compararlas y realizar el consecutivo
      $fechaBase =  $consecutivo->getFechaactual();
      $fechaBase =  $fechaBase->format('y-m-d');

      if ($fechaBase != $fechActual) {
        $partesFecha = explode('-', $fechActual);
        //Extraigoel dia de la fecha para el consecutivo de la maquina
        $dia = $partesFecha[2]; 
        $fechaConse = DateTime::createFromFormat('y-m-d', $fechActual);
        $prefijo =  $fechaConse->format('ymd');
        //Creo el numero para la orden con la fecha actual
       // $numeroOrden = $consecutivo->getPrefijo() . '' . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        $numeroOrden = $prefijo . '' . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        //Creo el  nuevo prefijo para la maquina
        $conseMaquina = $dia . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        $Orden->setNumero($numeroOrden);
        $Orden->setIdConMaquina($conseMaquina);        
        //Actualiza el número consecutivo actual
        $consecutivo->setConsecutivo($consecutivo->getConsecutivo() + 1);
        $consecutivo->setFechaactual(new \DateTime($fechActual));
        $consecutivo->setPrefijo($prefijo);
        $em->persist($Orden);
        $em->flush();
      } else {

        $partesFecha = explode('-', $fechActual);
        //Extraigoel dia de la fecha para el consecutivo de la maquina
        $dia = $partesFecha[2]; 
        $fechaConse = DateTime::createFromFormat('y-m-d', $fechActual);
        $prefijo =  $fechaConse->format('ymd');
        //Creo el numero para la orden con la fecha actual
       // $numeroOrden = $consecutivo->getPrefijo() . '' . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        $numeroOrden = $prefijo . '' . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        //Creo el  nuevo prefijo para la maquina
        $conseMaquina = $dia . str_pad($numConsecutivo, 6, '0', STR_PAD_LEFT);
        $Orden->setNumero($numeroOrden);
        $Orden->setIdConMaquina($conseMaquina);        
        //Actualiza el número consecutivo actual
        $consecutivo->setConsecutivo($consecutivo->getConsecutivo() + 1);                
        $em->persist($Orden);
        $em->flush();
      }
      $response = array(
        'status' => 'success',
        'code' => '400',
        'msg' => 'Número de orden ' . $Orden->getNumero() . ' creado satisfactoriamente.' . $conseMaquina . '-' . $fechaBase . '-' . $fechActual,
        'data' => $data,
      );

      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create Orden.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditOrdenAction(Request $request,Orden $Orden)
  {
    $form = $this->createForm(OrdenType::class, $Orden);
    $data = json_decode($request->getContent(), true);
    $data['fecha'] = new \DateTime($data['fecha']);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($Orden);
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
   * Show Orden.
   * @Rest\Get("/{numeroOrden}/show")
   *
   * @return Response
   */
  public function getShowOrdenAction($numeroOrden)
  {
    $em = $this->getDoctrine()->getManager();
    $repository = $this->getDoctrine()->getRepository(Orden::class);

    $orden =  $repository->findOneBy(
      array('activo' => true, 'numero' => $numeroOrden)
    );

    if ($numeroOrden == "undefined") {
      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();
      $numOrden = $consecutivo->getPrefijo().''.$numConsecutivo;

      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Registro no encontrado',
          'consecutivoOrden' => $numOrden, 
          'numConsecutivo' => $numConsecutivo, 
          'prefijo' => $consecutivo->getPrefijo(), 
        );
      }
     
    if ($orden) {
      $ordenResponse = array(
        'id' => $orden->getId(),
        'emb' => $orden->getEmb(),
        'numero' => $orden->getNumero(),
        'numExterno' => $orden->getNumExterno(),
        'servicio' => $orden->getServicio(),
        'cama' => $orden->getCama(),
        'diagnostico' => $orden->getDiagnostico(),
        'notas' => $orden->getNotas(),
        'medico' => $orden->getMedico(),
        'paciente' => $orden->getPaciente(),
        'eapb' => $orden->getEapb(),
        'anio' => $orden->getFecha()->format('Y'),
        'mes' => $orden->getFecha()->format('m'),
        'dia' => $orden->getFecha()->format('d'),
        'fecha' => $orden->getFecha()->format('Y-m-d'),
      );
      $response = array(
          'status' => 'success', 
          'code' => '200', 
          'msg' => 'Registro encontrado', 
          'data' => $ordenResponse
      );
    }else{

      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();
      $numOrden = $consecutivo->getPrefijo().''.$numConsecutivo;

      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro no encontrado',
        'consecutivoOrden' => $numOrden, 
        'numConsecutivo' => $numConsecutivo, 
        'prefijo' => $consecutivo->getPrefijo(), 
      );
    }
    return $this->handleView($this->view($response));
  }


  /**
   * Show Orden.
   * @Rest\Get("/{ordenId}/show/id/{sentido}")
   *
   * @return Response
   */
  public function getShowOrdenIdAction($ordenId, $sentido)
  {
    $em = $this->getDoctrine()->getManager();
    $repository = $this->getDoctrine()->getRepository(Orden::class);

    $orden =  $repository->findOneBy(
      array('activo' => true, 'id' => $ordenId)
    ); 

    $ordenIdUltimo =  $repository->findByUltimo(); 

    if ($ordenId > $ordenIdUltimo['1']) {
      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();
      $numOrden = $consecutivo->getPrefijo().''.$numConsecutivo;

      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'No hay mas registros',
        'consecutivoOrden' => $numOrden, 
      );

      return $this->handleView($this->view($response));
    }

    while (!$orden and $ordenId >= 0) {
      if ($sentido == 'menos') {
        $ordenId = $ordenId - 1;
      }else{
        $ordenId = $ordenId + 1;
      }

      $orden =  $repository->findOneBy(
        array('activo' => true, 'id' => $ordenId)
      ); 
    }

    if ($ordenId == "undefined") {
      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();
      $numOrden = $consecutivo->getPrefijo().''.$numConsecutivo;

      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Registro no encontrado',
          'consecutivoOrden' => $numOrden, 
        );
    }
     
    if ($orden) {
     
      $response = array(
          'status' => 'success', 
          'code' => '200', 
          'msg' => 'Registro encontrado', 
          'data' => $orden
      );
    }else{
      $consecutivoRepository = $this->getDoctrine()->getRepository(Consecutivo::class);
      $consecutivo = $consecutivoRepository->find(1);
      $numConsecutivo = $consecutivo->getConsecutivo();
      $numOrden = $consecutivo->getPrefijo().''.$numConsecutivo;

      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro no encontrado',
        'consecutivoOrden' => $numOrden, 
      );
    }
   
    return $this->handleView($this->view($response));
  }


  /**
   * 
   * @Rest\Get("/{id}/delete")
   *
   * @return Response
   */
  public function getDeleteOrdenAction(Orden $orden)
  {
    $em = $this->getDoctrine()->getManager();

    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);

    $ordenEstudios = $repository->findBy(
      array(
        'activo' => true, 
        'orden' => $orden->getId(),
        'estadoEstudio' => 2
      )
    );

    if (count($ordenEstudios) > 0) {
      $response = array(
        'status' => 'warning', 
        'code' => '200', 
        'msg' => 'Esta orden no se puede eliminar porque tiene '. count($ordenEstudios) .' estudios diligenciados.'
      );
    } else {
      if($orden->getActivo()){
        $orden->setActivo(false);
      }else{
        $orden->setActivo(true);
      }
      
      $em->flush();
      
      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Registro eliminado',
          'data' => $orden
      );
    }
    
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getOrdenSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Orden::class);
    $registros = $repository->findByActivo(true);
    $registrosArray= null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getNombre(), 
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
   * @Rest\Get("/find/by/{numero}/numero")
   *
   * @return Response
   */
  public function findOrdenByNumeroAction($numero)
  {
    //Reviso si la orden existe 
    $repository = $this->getDoctrine()->getRepository(Orden::class);        
    $registro = $repository->findOneByNumero($numero);

    if ($registro) {

      //Consulto la 
      $ordenEstudioRepository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
      $queryBuilder = $ordenEstudioRepository->createQueryBuilder('oe');
      $queryBuilder->where('oe.activo = :activo')
          ->andWhere('oe.orden = :ordenId')          
          ->setParameter('activo', true)
          ->setParameter('ordenId', $registro->getId());
      
      
      $ordenEstudios = $queryBuilder->getQuery()->getResult();   
      
      $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);


      $valoresEstudioArray = null;
      foreach ($ordenEstudios as $key => $ordenEstudio) {
        if ($ordenEstudio->getUser()) {
          $valoresEstudio =  $valorResultadoRepository->findBy(
            array(
              'activo' => true,
              'ordenEstudio' => $ordenEstudio->getId()
            )
          );
          foreach ($valoresEstudio as $key => $ve) {
            $valoresEstudioArray[] = array(
              'id' => $ve->getId(),
              'resultado_estudio' => $ve->getResultadoEstudio(),
              'valor' => substr($ve->getValor(), 0, ((strpos($ve->getValor(), '.') + 1) + 2)),
              'ordenEstudio' => $ve->getOrdenestudio()->getEstudio()->getId(),
            );
          }
        }
      }

      $anos = $this->busca_edad($registro->getPaciente()->getFechaNacimiento()->format('Y-m-d'));
      $arrayFinal = array(
        'orden' => $registro, 
        'valoresEstudio' => $valoresEstudioArray, 
        'ordenEstudios' => $ordenEstudios, 
        'anios' => $anos, 
      );

      $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Orden encontrada', 
        'data' => $arrayFinal,
      );

        
    }else{
      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Orden no encontrada', 
      );
    }   
    return $this->handleView($this->view($response));
  }

  /**
   * Search Ordenes by id paciente.
   * @Rest\Post("/search/by/patient")
   *
   * @return Response
   */
  public function searchByPatientAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);

    $em = $this->getDoctrine()->getManager();     
    
    $repository = $this->getDoctrine()->getRepository(Orden::class);
    $ordenes = $repository->findBy(
      array('paciente' => $data['idPaciente']),
      array('fecha' => 'DESC')
    );
    
    if ($ordenes) {
      $response = array(
        'status' => 'success', 
        'code' => '200', 
        'message' => count($ordenes).' encontradas satisfactoriamente.',
        'data' => array(
          'ordenes' => $ordenes
        )
      );
    } else {
      $response = array(
        'status' => 'warning', 
        'code' => '400', 
        'message' => 'No existen ordenes vinculadas al paciente.'
      );
    }
    
    return $this->handleView($this->view($response, Response::HTTP_CREATED));
    
  }




  /**
   * 
   * @Rest\Get("/find/by/{numero}/numero/pdf")
   * 
   * @return Response
   */
  public function findOrdenByNumeroPdfAction($numero)
  {

    //Funcion que imprime los resultados 
    $repository = $this->getDoctrine()->getRepository(Orden::class);
    
    $orden = $repository->findOneByNumero($numero);

    if ($orden) {
      $ordenEstudioRepository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
      $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);

      $ordenEstudios =  $ordenEstudioRepository->findBy(
        array('activo' => true, 
        'orden' => $orden->getId(), 
        'estadoEstudio' => 2        
      ));

      $valoresEstudioArray = null;

      if (count($ordenEstudios) > 0) {
        foreach ($ordenEstudios as $key => $ordenEstudio) {
          $valoresEstudio =  $valorResultadoRepository->findBy(
            array('activo' => true, 'ordenEstudio' => $ordenEstudio->getId())
          );
          
          foreach ($valoresEstudio as $key => $ve) {
            if ($ve->getValor() != '') {
              $valoresEstudioArray[] = array(
                'id'=>$ve->getId(), 
                'resultado_estudio'=>$ve->getResultadoEstudio(),
                'valor'=>  $ve->getValor(),
                'fechaModificacion'=>  $ve->getFechaModificacion(),
                'ordenEstudio'=>$ve->getOrdenestudio()->getEstudio()->getId(),
              );
            }
          }
        }
  
        $anios = $this->busca_edad($orden->getPaciente()->getFechaNacimiento()->format('Y-m-d'));
  
        $paciente =  array(
          'nombres' => $orden->getPaciente()->getUser()->getNombres() .' '. $orden->getPaciente()->getUser()->getApellidos(), 
          'identificacion' => $orden->getPaciente()->getUser()->getIdentificacion(), 
          'genero' => $orden->getPaciente()->getUser()->getGenero(), 
          'telefono' => $orden->getPaciente()->getUser()->getTelefono(), 
          'direccion' => $orden->getPaciente()->getDireccion(), 
          'fecha' => $orden->getFecha()->format('d/m/Y'), 
          'hora' => $orden->getFecha()->format('h:i:s A'), 
          'fechaFin' => $orden->getFecha()->format('Y-m-d H:i:s'), 
          'fechaNacimiento' => $orden->getPaciente()->getFechaNacimiento()->format('d/m/Y'), 
          'embarazo' => $orden->getEmb(), 
          'anios' => $anios, 
          'servicio' => $orden->getServicio()->getNombre()
        );
  
        $arrayFinal = array(
          'orden' => $orden, 
          'valoresEstudio' => $valoresEstudioArray, 
          'ordenEstudios' => $ordenEstudios, 
          'anios' => $anios,
          'paciente' => $paciente,
        );
  
        $response = array(
          'status' => 'success', 
          'code' => '200', 
          'msg' => 'Orden encontrada', 
          'data' => $arrayFinal, 
        );
  
        $html = $this->renderView('reportes/order_full.html.twig', array('arrayFinal' => $arrayFinal));
        
        $repository = $this->getDoctrine()->getRepository(Institucion::class);
        $institucion = $repository->findOneByActivo(true);
  
        if (!$institucion) {
          $institucion = array(
            'nombre' => 'Clinimetrics',
            'identificacion' => '814000337',
            'direccion' => 'Carrera 2 # 16 - 08 La Unión, Nariño',
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
  
        //Actulizat la fecha de impresion
        ////

        $repositoryOrden = $this->getDoctrine()->getRepository(Orden::class);    
        $ordenAct = $repositoryOrden->findOneByNumero($numero);

        $ordenAct->setFechaImp(new \DateTime('now'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($ordenAct);
        $em->flush();

        ////////


        $data = array(
          'numero' => $ordenEstudio->getOrden()->getNumero(), 
          'paciente' => $paciente,
          'institucion' => $institucion
        );
  
        $nombrePdf = $this->get('app.pdf.template.portrait')->template(
          $html, $data
        );
      } else {
        $response = array(
          'status' => 'warning', 
          'code' => '204',  
          'msg' => 'No hay estudios diligenciados.', 
        );
      }
    }else{
      $response = array(
        'status' => 'success', 
        'code' => '400',  
        'msg' => 'Orden no encontrada', 
      );
    }
   
    return $this->handleView($this->view($response));
  }

  /**
   * 
   * @Rest\Get("/work/sheet/by/estudio/{idEstudio}/{desde}/{hasta}/pdf")
   * 
   * @return Response
   */
  public function workSheetByOrdenEstudioPdfAction($idEstudio,$desde,$hasta,EntityManagerInterface $entityManager)
  {
    try {
      $repository = $this->getDoctrine()->getRepository(Estudio::class);
      $estudio = $repository->findOneById($idEstudio);

      $from = \DateTime::createFromFormat("Y-m-d H:i:s", $desde . " 00:00:00");
      $to   =  \DateTime::createFromFormat("Y-m-d H:i:s", $hasta . " 23:59:59");

      $queryBuilder = $entityManager->createQueryBuilder();

      $queryBuilder->select('oe')
      ->from(OrdenEstudio::class, 'oe')
      ->innerJoin(Orden::class, 'orden', 'WITH', 'oe.orden = orden.id')
      ->where('oe.activo = :activo')
      ->andWhere('oe.estadoEstudio = :estadoEstudio')
      ->andWhere('oe.estudio = :estudioId')
      ->andWhere(
        $queryBuilder->expr()->between('orden.fecha', ':desde', ':hasta')
      )
      ->setParameter('activo', true)
      ->setParameter('desde', $from)
      ->setParameter('hasta', $to)
      ->setParameter('estadoEstudio', 1)
      ->setParameter('estudioId', $estudio->getId());

      $query = $queryBuilder->getQuery();
      $ordenes = $query->getResult();
      ///    

      $data = [];
      foreach ($ordenes as $key => $ordenEstudio) {
        $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
        $resultados = $repository->getAllNoTitle($ordenEstudio->getEstudio()->getId());
        $anios = $this->busca_edad($ordenEstudio->getOrden()->getPaciente()->getFechaNacimiento()->format('Y-m-d'));
        if ($resultados) {
          $arrayResultado = array(
            'ordenEstudio' => $ordenEstudio,
            'anios' => $anios,
            'resultados' => $resultados
          );
          $data[] = $arrayResultado;
        }
      }

      if (count($data) > 0) {
        $response = array(
          'status' => 'success',
          'code' => '200',
          'message' => 'Existen resultados para este estudio.',
          'data' => $data
        );

        $repository = $this->getDoctrine()->getRepository(Institucion::class);
        $institucion = $repository->findOneByActivo(true);

        if (!$institucion) {
          $institucion = array(
            'nombre' => 'Clinimetrics',
            'identificacion' => '814000337',
            'direccion' => 'Carrera 2 # 16 - 08 La Unión, Nariño',
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

        $array = array(
          'data' => $data,
          'estudio' => $estudio,
          'desde'  => $desde,
          'hasta'  => $hasta,
        );

        $html = $this->renderView('reportes/work_sheet.html.twig', $array);

        $nombrePdf = $this->get('app.pdf.template.landscape')->templateWorkSheet($html, array(
          'data' => $data,
          'institucion' => $institucion,
        ));
      } else {
        $response = array(
          'status' => 'error',
          'code' => '400',
          'message' => 'No existen resultados para este estudio.',
        );
      }
      return $this->handleView($this->view($response));
    } catch (\Exception $e) {
      $response = array(
        'status' => 'error',
        'code' => '400',
        'msg' => 'Error al generar la consulta',
        'data' =>  $e->getMessage()
      );
      return $this->handleView($this->view($response));
    }
  }

/**
   * 
   * @Rest\Get("/barcode/by/orden-estudio/{idOrdenEstudio}/pdf")
   * 
   * @return Response
   */
  public function printBarcodeByOrdenEstudioPdfAction($idOrdenEstudio)
  {
    $ordenEstudioRepository = $this->getDoctrine()->getRepository(OrdenEstudio::class);

    $ordenEstudio = $ordenEstudioRepository->findOneById($idOrdenEstudio);

    if ($ordenEstudio) {
      $repository = $this->getDoctrine()->getRepository(Institucion::class);
      $institucion = $repository->findOneByActivo(true);

      if (!$institucion) {
        $institucion = array(
          'nombre' => 'Clinimetrics',
          'identificacion' => '814000337',
          'direccion' => 'Carrera 2 # 16 - 08 La Unión, Nariño',
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
     
      $data = array(
        'ordenEstudio' => $ordenEstudio, 
        'institucion' => $institucion
      );

      $response = array(
        'status' => 'success', 
        'code' => '200',
        'msg' => 'Orden encontrada', 
        'data' => $data, 
      );

      $html = $this->renderView('reportes/barcode.html.twig', array(
        'data' => $data
      ));

      $data = array(
        'numero' => $ordenEstudio->getOrden()->getNumero()
      );

      $nombrePdf = $this->get('app.pdf.template.barcode')->template(
        $html,
        $data
      );
    } else {
      $response = array(
        'status' => 'error', 
        'code' => '400', 
        'msg' => 'Orden Estudio no encontrada.', 
      );
    }
   
    return $this->handleView($this->view($response));
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


    /**
   * 
   * @Rest\Get("/search/one/last/first/{orderBy}")
   *
   * @return Response
   */
  
   public function searchOneLastOrFirstAction($orderBy)
  {
    $repository = $this->getDoctrine()->getRepository(Orden::class);
    
    $orden = $repository->searchOneLastOrFisrt($orderBy);

    $anios = $this->busca_edad($orden->getPaciente()->getFechaNacimiento()->format('Y-m-d'));

    if ($orden) {
      $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Se ha cargado la última orden registrada con el No. '.$orden->getNumero(), 
        'data' => $orden
      );
    }else{
      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Ninguna orden registrada aún.', 
      );
    }
   
    return $this->handleView($this->view($response));
  }

  /**
   * 
   * @Rest\Get("/find/by/orden-estudio/{idOrdenEstudio}/pdf")
   * 
   * @return Response
   */
  public function printByOrdenEstudioPdfAction($idOrdenEstudio)
  {
    $ordenEstudioRepository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
    $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);

    $ordenEstudio = $ordenEstudioRepository->findOneById($idOrdenEstudio);

    if ($ordenEstudio) {
      $valoresEstudioArray = null;

      $valoresEstudio =  $valorResultadoRepository->findBy(
        array('activo' => true, 'ordenEstudio' => $ordenEstudio->getId())
      );
      
      foreach ($valoresEstudio as $key => $ve) {
        if ($ve->getValor() != '') {
          $valoresEstudioArray[] = array(
            'id'=>$ve->getId(), 
            'resultado_estudio'=>$ve->getResultadoEstudio(),
            'valor'=>  $ve->getValor(),
            'fechaModificacion'=>  $ve->getFechaModificacion(),
            'ordenEstudio'=>$ve->getOrdenestudio()->getEstudio()->getId(),
          );
        }
      }

      $anios = $this->busca_edad($ordenEstudio->getOrden()->getPaciente()->getFechaNacimiento()->format('Y-m-d'));

      $paciente =  array(
        'nombres' => $ordenEstudio->getOrden()->getPaciente()->getUser()->getNombres() .' '. $ordenEstudio->getOrden()->getPaciente()->getUser()->getApellidos(), 
        'identificacion' => $ordenEstudio->getOrden()->getPaciente()->getUser()->getIdentificacion(), 
        'genero' => $ordenEstudio->getOrden()->getPaciente()->getUser()->getGenero(), 
        'telefono' => $ordenEstudio->getOrden()->getPaciente()->getUser()->getTelefono(), 
        'direccion' => $ordenEstudio->getOrden()->getPaciente()->getDireccion(), 
        'fecha' => $ordenEstudio->getOrden()->getFecha()->format('d/m/Y'), 
        'hora' => $ordenEstudio->getOrden()->getFecha()->format('h:i:s A'), 
        'fechaFin' => $ordenEstudio->getOrden()->getFecha()->format('Y-m-d H:i:s'), 
        'fechaNacimiento' => $ordenEstudio->getOrden()->getPaciente()->getFechaNacimiento()->format('d/m/Y'), 
        'embarazo' => $ordenEstudio->getOrden()->getEmb(), 
        'anios' => $anios,
        'servicio' => $ordenEstudio->getOrden()->getServicio()->getNombre()
      );
     
      $arrayFinal = array(
        'valoresEstudio' => $valoresEstudioArray, 
        'ordenEstudio' => $ordenEstudio, 
        'anios' => $anios,
        'paciente' => $paciente,
      );

      $response = array(
        'status' => 'success', 
        'code' => '200',
        'msg' => 'Orden encontrada', 
        'data' => $arrayFinal, 
      );

      $repository = $this->getDoctrine()->getRepository(Institucion::class);
      $institucion = $repository->findOneByActivo(true);

      if (!$institucion) {
        $institucion = array(
          'nombre' => 'Clinimetrics',
          'identificacion' => '814000337',
          'direccion' => 'Carrera 2 # 16 - 08 La Unión, Nariño',
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

      $data = array(
        'numero' => $ordenEstudio->getOrden()->getNumero(), 
        'paciente' => $paciente,
        'institucion' => $institucion
      );

      $html = $this->renderView('reportes/order_single.html.twig', array(
        'arrayFinal' => $arrayFinal
      ));

      $nombrePdf = $this->get('app.pdf.template.portrait')->template(
        $html,
        $data
      );
    } else {
      $response = array(
        'status' => 'error', 
        'code' => '400', 
        'msg' => 'Orden Estudio no encontrada.', 
      );
    }
   
    return $this->handleView($this->view($response));
  }

  /**
   * 
   * @Rest\Get("/printer/ticket/{id}")
   * 
   * @return Response
   */
  public function printerAction($id)
  {
    try {
      $profile = CapabilityProfile::load("simple");
      $connector = new WindowsPrintConnector("SATRED");
      //$connector = new NetworkPrintConnector("192.168.0.35", 9100);
      $printer = new Printer($connector);
      //$printer = new Printer($connector, $profile);

      /* Initialize */
      $printer->initialize();

      /* Text */
      $printer->text("Hello world");

      $printer -> setBarcodeHeight(80);
      $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
      $printer -> barcode("9876");

      /* Pulse */
      $printer -> pulse();

      /* Always close the printer! On some PrintConnectors, no actual
      * data is sent until the printer is closed. */
      $printer->close();
    } catch (Exception $e) {
      echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
      var_dump($e->getMessage());
      die();
    }
  }

  /**
   *  
   * @Rest\Get("/impOrden/{id}")
   *
   * @return Response
   */
  public function EditImpresion($id)
  {
    $product = $this->getDoctrine()->getRepository(Orden::class)->find($id);

        if (!$product) {
          $response = array(
            'status' => 'error', 
            'code' => '500', 
            'msg' => 'No Se encontro Orden', 
          );
        }
        else
        {
          $product->setImporden(1);
          $em = $this->getDoctrine()->getManager();
          $em->persist($product);
          $em->flush();
          $response = array(
            'status' => 'sucess', 
            'code' => '200', 
            'msg' => ' Se encontro Orden', 
          );
        }         
  return $this->handleView($this->view($response));  
  }
    
}