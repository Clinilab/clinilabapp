<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\OrdenEstudio;
use App\Entity\EstadoEstudio;
use App\Entity\ResultadoEstudio;
use App\Form\OrdenEstudioType;
/**
 * OrdenEstudio controller.
 * @Route("/api/orden/estudio", name="api_")
 */
class OrdenEstudioController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/{idOrden}")
   *
   * @return Response
   */
  public function getOrdenEstudioAction($idOrden)
  {
    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
    $registros =  $repository->findBy(
      array('activo' => true, 'orden' => $idOrden)
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
   * Create OrdenEstudio.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postOrdenEstudioAction(Request $request)
  {

    $OrdenEstudio = new OrdenEstudio();
    

    
    $form = $this->createForm(OrdenEstudioType::class, $OrdenEstudio);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $repositoryEstadoEstudio = $this->getDoctrine()->getRepository(EstadoEstudio::class);
      $estado = $repositoryEstadoEstudio->find(1);

      $repositoryOrdenEstudio = $this->getDoctrine()->getRepository(OrdenEstudio::class);


      $ordenEstudio = $repositoryOrdenEstudio->findBy(
        array('activo' => true, 'estudio' => $data['estudio'], 'orden' => $data['orden'])
      );

      if($ordenEstudio){
        $response = array(
          'status' => 'error', 
          'code' => '200', 
          'msg' => 'El estudio ya se encuentra registrado en la orden', 
        );
        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }


      $OrdenEstudio->setActivo(true);
      $OrdenEstudio->setEstadoEstudio($estado);
      $OrdenEstudio->setImp(1);
      $OrdenEstudio->setIdDetalle(0);
      $OrdenEstudio->setEstadoEnvio(0);
      $em->persist($OrdenEstudio);
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
   * Create OrdenEstudio.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditOrdenEstudioAction(Request $request,OrdenEstudio $OrdenEstudio)
  {
    $form = $this->createForm(OrdenEstudioType::class, $OrdenEstudio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($OrdenEstudio);
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
   * Show OrdenEstudio.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowOrdenEstudioAction(OrdenEstudio $registro)
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
  public function getDeleteOrdenEstudioAction(OrdenEstudio $OrdenEstudio)
  {
    $em = $this->getDoctrine()->getManager();
    if($OrdenEstudio->getActivo()){
      $OrdenEstudio->setActivo(false);
    }else{
      $OrdenEstudio->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $OrdenEstudio
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getOrdenEstudioSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class);
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
   *@Rest\Post("/list/by/estado")
   *
   * @return Response
   */
  public function listByEstadoAction(Request $request)
  {
    try {

      $conn = $this->getDoctrine()->getConnection();
      $data = json_decode($request->getContent(), true);

      $repository = $this->getDoctrine()->getRepository(EstadoEstudio::class);
      $estadoEstudio = $repository->findOneById($data[2]);

      $sql = "select  ss.nombre as 'estado',et.nombre as 'estudio',et.id as 'idEstudio',count(*) as 'total' from orden as  od
    inner join orden_estudio as os on (od.id = os.orden_id)
    INNER JOIN estudio et ON (et.id = os.estudio_id)
    inner join estado_estudio as ss on (ss.id = os.estado_estudio_id)
    where os.estado_estudio_id =1 and os.activo =1 and
    date(od.fecha)>= :fecha_ini and date(od.fecha)<= :fecha_fin
    group by  ss.nombre,et.nombre,et.id";

      $stEstudios = $conn->prepare($sql);
      $resultEstudios = $stEstudios->executeQuery(array('fecha_ini' => $data[0], 'fecha_fin' => $data[1]));
      $registros = $resultEstudios->fetchAllAssociative();

      $registrosArray = [];
      $repository = $this->getDoctrine()->getRepository(ResultadoEstudio::class);

      foreach ($registros as $key => $ordenEstudio) {
        $resultados = $repository->findBy(
          array(
            'activo' => true,
            'estudio' => $ordenEstudio['idEstudio']
          ),
          array('posicion' => 'ASC')
        );

        if ($resultados) {
          $registrosArray[] = $ordenEstudio;
        }
      }

      if (count($registrosArray) > 0) {
        $response = array(
          'status' => 'success',
          'code' => '200',
          'message' => count($registrosArray) . ' registros encontrados con estado: ' . $estadoEstudio->getNombre(),
          'data' => $registrosArray
        );
      } else {
        $response = array(
          'status' => 'error',
          'code' => '400',
          'message' => 'No existen ordenes con estado: ' . $estadoEstudio->getNombre()
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
   * @Rest\Post("/print")
   *
   * @return Response
   */
  public function printAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);
    $em = $this->getDoctrine()->getManager();

    $repository = $this->getDoctrine()->getRepository(OrdenEstudio::class)->find($data["id"]);
    $repository->setImp(0);
    $em->persist($repository);
    $em->flush();
    

    $response = array(
        'status' => 'success', 
        'code' => '200', 
        'msg' => 'Se ha agregado documento a impresión', 
        'data' => $data["id"]
    );
    return $this->handleView($this->view($response));
  }

}
