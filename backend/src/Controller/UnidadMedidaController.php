<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\UnidadMedida;
use App\Form\UnidadMedidaType;
/**
 * UnidadMedida controller.
 * @Route("/api/unidad/medida", name="api_")
 */
class UnidadMedidaController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getUnidadMedidaAction()
  {
    $repository = $this->getDoctrine()->getRepository(UnidadMedida::class);
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
   * Create UnidadMedida.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postUnidadMedidaAction(Request $request)
  {
    $unidadMedida = new UnidadMedida();
    $form = $this->createForm(UnidadMedidaType::class, $unidadMedida);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $unidadMedida->setActivo(true);
      $em->persist($unidadMedida);
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
   * Create UnidadMedida.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditUnidadMedidaAction(Request $request,UnidadMedida $unidadMedida)
  {
    $form = $this->createForm(UnidadMedidaType::class, $unidadMedida);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($unidadMedida);
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
   * Show UnidadMedida.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowUnidadMedidaAction(UnidadMedida $registro)
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
  public function getDeleteUnidadMedidaAction(UnidadMedida $unidadMedida)
  {
    $em = $this->getDoctrine()->getManager();
    if($unidadMedida->getActivo()){
      $unidadMedida->setActivo(false);
    }else{
      $unidadMedida->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $unidadMedida
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getUnidadMedidaSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(UnidadMedida::class);
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