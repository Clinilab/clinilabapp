<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\EstadoEstudio;
use App\Form\EstadoEstudioType;
/**
 * EstadoEstudio controller.
 * @Route("/api/estado/estudio", name="api_")
 */
class EstadoEstudioController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getEstadoEstudioAction()
  {
    $repository = $this->getDoctrine()->getRepository(EstadoEstudio::class);
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
   * Create EstadoEstudio.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postEstadoEstudioAction(Request $request)
  {
    $estadoEstudio = new EstadoEstudio();
    $form = $this->createForm(EstadoEstudioType::class, $estadoEstudio);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $estadoEstudio->setActivo(true);
      $em->persist($estadoEstudio);
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
   * Create EstadoEstudio.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditEstadoEstudioAction(Request $request,EstadoEstudio $estadoEstudio)
  {
    $form = $this->createForm(EstadoEstudioType::class, $estadoEstudio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($estadoEstudio);
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
   * Show EstadoEstudio.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowEstadoEstudioAction(EstadoEstudio $registro)
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
  public function getDeleteEstadoEstudioAction(EstadoEstudio $estadoEstudio)
  {
    $em = $this->getDoctrine()->getManager();
    if($estadoEstudio->getActivo()){
      $estadoEstudio->setActivo(false);
    }else{
      $estadoEstudio->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $estadoEstudio
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getEstadoEstudioSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(EstadoEstudio::class);
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
}