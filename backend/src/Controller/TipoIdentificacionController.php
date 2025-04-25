<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\TipoIdentificacion;
use App\Form\TipoIdentificacionType;
/**
 * TipoIdentificacion controller.
 * @Route("/api/tipo/identificacion", name="api_")
 */
class TipoIdentificacionController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getTipoIdentificacionAction()
  {
    $repository = $this->getDoctrine()->getRepository(TipoIdentificacion::class);
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
   * Create TipoIdentificacion.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postTipoIdentificacionAction(Request $request)
  {
    $tipoIdentificacion = new TipoIdentificacion();
    $form = $this->createForm(TipoIdentificacionType::class, $tipoIdentificacion);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $tipoIdentificacion->setActivo(true);
      $em->persist($tipoIdentificacion);
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
   * Create TipoIdentificacion.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditTipoIdentificacionAction(Request $request,TipoIdentificacion $tipoIdentificacion)
  {
    $form = $this->createForm(TipoIdentificacionType::class, $tipoIdentificacion);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($tipoIdentificacion);
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
   * Show TipoIdentificacion.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowTipoIdentificacionAction(TipoIdentificacion $registro)
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
  public function getDeleteTipoIdentificacionAction(TipoIdentificacion $tipoIdentificacion)
  {
    $em = $this->getDoctrine()->getManager();
    if($tipoIdentificacion->getActivo()){
      $tipoIdentificacion->setActivo(false);
    }else{
      $tipoIdentificacion->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $tipoIdentificacion
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getTipoIdentificacionSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(TipoIdentificacion::class);
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