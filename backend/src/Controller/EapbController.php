<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Eapb;
use App\Form\EapbType;
/**
 * Eapb controller.
 * @Route("/api/eapb", name="api_")
 */
class EapbController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getEapbAction()
  {
    $repository = $this->getDoctrine()->getRepository(Eapb::class);
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
   * Create Eapb.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postEapbAction(Request $request)
  {
    $eapb = new Eapb();
    $form = $this->createForm(EapbType::class, $eapb);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $eapb->setActivo(true);
      $eapb->setFrecuencia(0);
      $em->persist($eapb);
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
   * Create Eapb.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditEapbAction(Request $request,Eapb $eapb)
  {
    $form = $this->createForm(EapbType::class, $eapb);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($eapb);
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
   * Show Eapb.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowEapbAction(Eapb $registro)
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
  public function getDeleteEapbAction(Eapb $eapb)
  {
    $em = $this->getDoctrine()->getManager();
    if($eapb->getActivo()){
      $eapb->setActivo(false);
    }else{
      $eapb->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $eapb
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getEapbSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Eapb::class);

    $registros = $repository->findBy(array('activo'=>1),array('frecuencia' => 'DESC'));
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