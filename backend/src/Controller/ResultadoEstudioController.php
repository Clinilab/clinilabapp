<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\DBAL\Exception\DriverException;
use App\Entity\ResultadoEstudio;
use App\Entity\ValorResultado;
use App\Entity\OrdenEstudio;
use App\Form\ResultadoEstudioType;

/**
 * ResultadoEstudio controller.
 * @Route("/api/resultado/estudio", name="api_")
 */
class ResultadoEstudioController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getResultadoEstudioAction()
  {

   /* $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
    

    $registros = $repository->findByActivo(true);

    
    $response = array(
        'status' => 'success', 
        'code' => 400, 
        'msg' => 'Listado de registros', 
        'data' => $registros
    );

    return $this->handleView($this->view($response));*/

    $entityManager = $this->getDoctrine()->getManager();
    $connection = $entityManager->getConnection();
    $sql = "SELECT a.*  FROM resultado_estudio a  WHERE a.activo = :activo";
    $stmt = $connection->prepare($sql);
    $stmt->bindValue('activo', 1);
    $stmt->execute();

    $errores=[];
    $datos = [];
    while ($registro = $stmt->fetch()) {
      try {
        // Intenta deserializar los datos del registro
        $datosDeserializados = unserialize($registro['rangos']); 
        // Realiza alguna operación con los datos deserializados
      } catch (\Exception $e) {        
        $errorMensaje = "Error en el registro con ID " . $registro['id'] . ": " . $e->getMessage();
        $errores[] = $errorMensaje; 
      }
    }

    if (count($errores)>0  ){

      $response = array(
        'status' => false, 
        'code' => 200,
        'msg' => 'Listado de errores', 
        'total' => count($errores), 
        'data' => $errores
       );      

      return $this->handleView($this->view($response));
    }else{

   $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);   
   $registros = $repository->findByActivo(true);


      $response = array(
        'status' => 'success', 
        'code' => 400, 
        'msg' => 'Listado de registros',
        'data'=> $registros);

        

        return $this->handleView($this->view($response));
    }
}

  /**
   * 
   * @Rest\Get("/show/{orden}")
   *
   * @return Response
   */
  public function getResultadoEstudioEstudioOrdenAction($orden)
  {

    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);

    $odenEstudios = $repository->findBy(
      array('activo' => true, 'orden' => $orden)
    );
    
    $registrosArray = null;
    foreach ($odenEstudios as $key => $odenEstudio) {
      $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
        
      $registros = $repository->findBy(
        array('activo' => true, 'estudio' => $odenEstudio->getEstudio()->getId())
      );

      foreach ($registros as $key => $registro) {
        $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);
        
        $valorResultado = $valorResultadoRepository->findOneBy(
          array('activo' => true, 'resultadoEstudio' => $registro->getId(), 'ordenEstudio'=>$odenEstudio->getId())
        );
  
        if ($valorResultado) {
          $valor = $valorResultado->getValor();
        }else{
          $valor ='';
        }

        if ($odenEstudio->getUser()) {
          $userId = $odenEstudio->getUser()->getId();
        }else{
          $userId = null;
        }
  
        $registrosArray[] = array(
          'id' => $registro->getId(),
          'estudio' => $odenEstudio->getEstudio()->getId(),
          'estudioNombre' => $odenEstudio->getEstudio()->getNombre(),
          'user' => $userId,
          'nota' => $registro->getNota(),
          'nombre' => $registro->getNombre(),
          'valor' => $valor,
          'tipo' => $registro->getTipo(),
          'variableMaquina' => $registro->getVariableMaquina(),
          'unidadMedida' => $registro->getUnidadMedida()->getId(),
          'unidadMedidaNombre' => $registro->getUnidadMedida()->getNombre(),
          'unidadMedidaSimbolo' => $registro->getUnidadMedida()->getSimbolo(),
          'opciones' => $registro->getOpciones(),
          'ordenEstudio' => $odenEstudio->getId(),
          'formula' => $registro->getFormula(),
        );
        
      }
    }

      if ($registrosArray) {
        $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'Listado de registros', 
            'data' => $registrosArray
        );
      }else{
        $response = array(
          'status' => 'success', 
          'code' => '200', 
          'msg' => 'No hay parametros para este estudio', 
      );
      }
      return $this->handleView($this->view($response));

    
  }

  /**
   * Create ResultadoEstudio.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postResultadoEstudioAction(Request $request)
  {
    $resultadoEstudio = new ResultadoEstudio();
    $form = $this->createForm(ResultadoEstudioType::class, $resultadoEstudio);

    $data = json_decode($request->getContent(), true);



    $form->submit($data['resultadoEstudio']);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $resultadoEstudio->setActivo(true);

      $select = null;

      foreach ($data['opciones']['value'] as $key => $opcion) {

        if ($opcion === "variableMaquina") {
          $opcion = trim($opcion);
            // Convierte a mayúsculas
            $opcion = strtoupper($opcion);
            // Elimina todos los espacios en blanco en medio de los caracteres
            $opcion = preg_replace('/\s+/', '', $opcion);
      }

        $select[]  = array('id'=>$opcion,'text'=>$opcion);
      }

       //Consulto la variable maquina para revisar que no exista en el resultado 
       $varMaquina = $data['resultadoEstudio']['variableMaquina']; // Corregido el nombre de la variable
       $varMaquina = strtoupper(str_replace(' ', '', $varMaquina));
       $reposMaquina = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
       $resVarMaquina = $reposMaquina->findBy(
        array('variableMaquina' => $varMaquina, 'estudio' => $data['resultadoEstudio']['estudio'])
       );

      if ($resVarMaquina){
          $response = array(
            'status' => 'success', 
            'code' => '200', 
            'message' => 'la variable maquina para el estudio ya existe', 
          );
          return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }

      if ($data['resultadoEstudio']['tipo']=='selector' or $data['resultadoEstudio']['tipo']=='boolean') {
        if ($select) {          
          $resultadoEstudio->setOpciones($select);
        }else {
          $response = array(
            'status' => 'success', 
            'code' => '200', 
            'message' => 'debe de tener al menos una opción para el tipo selector', 
          );
          return $this->handleView($this->view($response, Response::HTTP_CREATED));
        }
      }

      if (count($data['rangos']) !=0) {
        $resultadoEstudio->setRangos($data['rangos']);
      }

      $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
      $paremtrosByEstudio = $repository->findBy(
        array('activo' => true, 'estudio' => $data['resultadoEstudio']['estudio'])
      );

      $resultadoEstudio->setPosicion(count($paremtrosByEstudio) + 1);
     
      $em->persist($resultadoEstudio);

      $em->flush();

      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Registro guardado con éxito', 
      );

      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create ResultadoEstudio.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditResultadoEstudioAction(Request $request,ResultadoEstudio $resultadoEstudio)
  {
    $form = $this->createForm(ResultadoEstudioType::class, $resultadoEstudio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data['resultadoEstudio']);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $select = null;
      
      foreach ($data['opciones']['value'] as $key => $opcion) {
        if ($opcion === "variableMaquina") {

            $opcion = trim($opcion);
            // Convierte a mayúsculas
            $opcion = strtoupper($opcion);
            // Elimina todos los espacios en blanco en medio de los caracteres
            $opcion = preg_replace('/\s+/', '', $opcion);
      }
      $select[]  = array('id'=>$opcion,'text'=>$opcion);
      }


         //Consulto la variable maquina para revisar que no exista en el resultado 
         $varMaquina = $data['resultadoEstudio']['variableMaquina']; // Corregido el nombre de la variable
         $varMaquina = strtoupper(str_replace(' ', '', $varMaquina));
       /*  $reposMaquina = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
         $resVarMaquina = $reposMaquina->findBy(
          array('variableMaquina' => $varMaquina, 'estudio' => $data['resultadoEstudio']['estudio'], 'Id' => $data['resultadoEstudio']['id'] )
         );*/


         $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
         $queryBuilder = $repository->createQueryBuilder('re');
         
         $resVarMaquina = $queryBuilder
             ->where('re.variableMaquina = :varMaquina')
             ->andWhere('re.estudio = :estudio')
             ->andWhere('re.id != :id')
             ->setParameter('varMaquina', $varMaquina)
             ->setParameter('estudio', $data['resultadoEstudio']['estudio'])
             ->setParameter('id', $data['resultadoEstudio']['id'])
             ->getQuery()
             ->getResult();
  
         //, 'Id' => $data['resultadoEstudio']['Id'] 
        if ($resVarMaquina){
            $response = array(
              'status' => 'success', 
              'code' => 200, 
              'message' => 'la variable maquina para el estudio ya existe: '.$varMaquina.' id:'.$data['resultadoEstudio']['id'] ,  
            );
            return $this->handleView($this->view($response, Response::HTTP_CREATED));
        }

      if ($data['resultadoEstudio']['tipo']=='selector' or $data['resultadoEstudio']['tipo']=='boolean') {
        if ($select) {
          $resultadoEstudio->setOpciones($select);
        }else {
          $response = array(
            'status' => 'success', 
            'code' => 200, 
            'message' => 'debe de tener al menos una opción para el tipo selector', 
          );
          return $this->handleView($this->view($response, Response::HTTP_CREATED));
        }
      }

      if (count($data['rangos']) !=0) {
        $resultadoEstudio->setRangos($data['rangos']);
      }
 
      $em->persist($resultadoEstudio);
      $em->flush();
      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'message' => 'Registro creado' 
      );
      return $this->handleView($this->view($response));
    }
    return $this->handleView($this->view($form->getErrors()));
  }
 
  /**
   * Show ResultadoEstudio.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowResultadoEstudioAction(ResultadoEstudio $registro)
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
  public function getDeleteResultadoEstudioAction(ResultadoEstudio $resultadoestudio)
  {
    $em = $this->getDoctrine()->getManager();

    if($resultadoestudio->getActivo()){
      $resultadoestudio->setActivo(false);
    }else{
      $resultadoestudio->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado.',
        'data' => $resultadoestudio
    );

    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getResultadoEstudioSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
    $registros = $repository->findByActivo(true);
    $registros = $repository->findBy(
      array('activo' => true, 'tipo' => 'number')
    );
    $registrosArray= null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getNombre(),
            'text' => $r->getEstudio()->getCodigo(). '/' .$r->getNombre(), 
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
   * @Rest\Get("/list/orden-estudio/{idOrdenEstudio}")
   *
   * @return Response
   */
  public function listByOrdenEstudioAction($idOrdenEstudio)
  {
    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
    $odenEstudio = $repository->findOneById($idOrdenEstudio);

    $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
    $registros = $repository->findBy(
      array(
        'activo' => true, 
        'estudio' => $odenEstudio->getEstudio()->getId()
      ),
      array('posicion' => 'ASC')
    );

    $registrosArray = null;

    foreach ($registros as $key => $registro) {
      $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);
      
      $valorResultado = $valorResultadoRepository->findOneBy(
        array(
          'activo' => true, 
          'resultadoEstudio' => $registro->getId(), 
          'ordenEstudio'=>$odenEstudio->getId()
        )
      );

      if ($valorResultado) {
        $valor = $valorResultado->getValor();
      }else{
        $valor ='';
      }

      if ($odenEstudio->getUser()) {
        $userId = $odenEstudio->getUser()->getId();
      }else{
        $userId = null;
      }

      $registrosArray[] = array(
        'id' => $registro->getId(),
        'estudioNombre' => $odenEstudio->getEstudio()->getNombre(),
        'nota' => $registro->getNota(),
        'nombre' => $registro->getNombre(),
        'tipo' => $registro->getTipo(),
        'variableMaquina' => $registro->getVariableMaquina(),
        'unidadMedida' => $registro->getUnidadMedida()->getId(),
        'unidadMedidaNombre' => $registro->getUnidadMedida()->getNombre(),
        'unidadMedidaSimbolo' => $registro->getUnidadMedida()->getSimbolo(),
        'opciones' => $registro->getOpciones(),
        'formula' => $registro->getFormula(),
        'valor' => $valor,
        'estudio' => $odenEstudio->getEstudio()->getId(),
        'ordenEstudio' => $odenEstudio->getId(),
        'user' => $userId,
      );
    }
    
    if ($registrosArray) {
      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Listado de registros', 
        'data' => $registrosArray
      );
    }else{
      $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'No hay parametros registrados para este estudio.', 
      );
    }
     
    return $this->handleView($this->view($response));    
  }

  /**
   * 
   * @Rest\Get("/list/estudio/{idEstudio}")
   *
   * @return Response
   */
  public function listByEstudioAction($idEstudio)
  {
    $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);

    $registros = $repository->findBy(
      array(
        'activo' => true, 
        'estudio' => $idEstudio
      ),
      array('posicion' => 'ASC')
    );
    
    if ($registros) {
      $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Listado de registros', 
        'data' => $registros
      );
    }else{
      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'No hay parametros registrados para este estudio.', 
      );
    }
     
    return $this->handleView($this->view($response));    
  }

  /**
   * 
   * @Rest\Get("/print/{idOrdenEstudio}/pdf")
   * 
   * @return Response
   */
  public function printPdfAction($idOrdenEstudio)
  {
    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
    $odenEstudio = $repository->findOneById($idOrdenEstudio);

    $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);
    $registros = $repository->findBy(
      array(
        'activo' => true, 
        'estudio' => $odenEstudio->getEstudio()->getId()
      )
    );

    $registrosArray = null;

    foreach ($registros as $key => $registro) {
      $valorResultadoRepository = $this->getDoctrine()->getRepository(ValorResultado::class);
      
      $valorResultado = $valorResultadoRepository->findOneBy(
        array(
          'activo' => true, 
          'resultadoEstudio' => $registro->getId(), 
          'ordenEstudio'=>$odenEstudio->getId()
        )
      );

      if ($valorResultado) {
        $valor = $valorResultado->getValor();
      }else{
        $valor ='';
      }

      if ($odenEstudio->getUser()) {
        $userId = $odenEstudio->getUser()->getId();
      }else{
        $userId = null;
      }

      $registrosArray[] = array(
        'id' => $registro->getId(),
        'estudioNombre' => $odenEstudio->getEstudio()->getNombre(),
        'nota' => $registro->getNota(),
        'nombre' => $registro->getNombre(),
        'tipo' => $registro->getTipo(),
        'variableMaquina' => $registro->getVariableMaquina(),
        'unidadMedida' => $registro->getUnidadMedida()->getId(),
        'unidadMedidaNombre' => $registro->getUnidadMedida()->getNombre(),
        'unidadMedidaSimbolo' => $registro->getUnidadMedida()->getSimbolo(),
        'opciones' => $registro->getOpciones(),
        'formula' => $registro->getFormula(),
        'valor' => $valor,
        'estudio' => $odenEstudio->getEstudio()->getId(),
        'ordenEstudio' => $odenEstudio->getId(),
        'user' => $userId,
      );
    }

    if ($registro) {
      $html = $this->renderView('reportes/single.html.twig', array('arrayFinal' => $arrayFinal));
      
      $nombrePdf = $this->get('app.pdf.template.portrait')->template($html, $registro->getNumero(), $paciente);
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
   * Ordered ResultadoEstudio by estudio.
   * @Rest\Post("/ordered")
   *
   * @return Response
   */
  public function postOrderedResultadoEstudioAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);

    $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);

    $em = $this->getDoctrine()->getManager();

    foreach ($data['parametros'] as $key => $resultadoEstudio) {
      $parametro = $repository->findOneById($resultadoEstudio['id']);

      $parametro->setPosicion($key + 1);
    }

    $em->flush();

    $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Parametros actualizados satisfactoriamente.'
    );

    return $this->handleView($this->view($response));
  }
}