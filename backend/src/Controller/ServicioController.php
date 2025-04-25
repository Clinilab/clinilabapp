<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Servicio;
use App\Form\ServicioType;
/**
 * Servicio controller.
 * @Route("/api/servicio", name="api_")
 */
class ServicioController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getServicioAction()
  {
    $repository = $this->getDoctrine()->getRepository(Servicio::class);
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
   * Create Servicio.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postServicioAction(Request $request)
  {
    $servicio = new Servicio();
    $form = $this->createForm(ServicioType::class, $servicio);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $servicio->setActivo(true);
      $em->persist($servicio);
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
   * Create Servicio.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditServicioAction(Request $request,Servicio $servicio)
  {
    $form = $this->createForm(ServicioType::class, $servicio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($servicio);
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
   * Show Servicio.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowServicioAction(Servicio $registro)
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
  public function getDeleteServicioAction(Servicio $servicio)
  {
    $em = $this->getDoctrine()->getManager();
    if($servicio->getActivo()){
      $servicio->setActivo(false);
    }else{
      $servicio->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $servicio
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getServicioSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Servicio::class);
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