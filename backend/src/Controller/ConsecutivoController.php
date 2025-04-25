<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Consecutivo;
use App\Form\ConsecutivoType;
use DateTime;

/**
 * Consecutivo controller.
 * @Route("/api/consecutivo", name="api_")
 */
class ConsecutivoController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getConsecutivoAction()
  {
    $repository = $this->getDoctrine()->getRepository(Consecutivo::class);
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
   * Create Consecutivo.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postConsecutivoAction(Request $request)
  {
    $consecutivo = new Consecutivo();
    $form = $this->createForm(ConsecutivoType::class, $consecutivo);
    $data = json_decode($request->getContent(), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $consecutivo->setActivo(true);
      $em->persist($consecutivo);
      $em->flush();
        $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'Registro creadoxx', 
        );
      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create Consecutivo.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditConsecutivoAction(Request $request,Consecutivo $consecutivo)
  {
    $form = $this->createForm(ConsecutivoType::class, $consecutivo);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($consecutivo);
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
   * Show Consecutivo.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowConsecutivoAction(Consecutivo $registro)
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
  public function getDeleteConsecutivoAction(Consecutivo $consecutivo)
  {
    $em = $this->getDoctrine()->getManager();
    if($consecutivo->getActivo()){
      $consecutivo->setActivo(false);
    }else{
      $consecutivo->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $consecutivo
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getConsecutivoSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Consecutivo::class);
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
   * @Rest\Get("/consecauto")
   *
   * @return Response
   */
  public function getConsecutivoAutomatico()
  {
      try 
      {
              $fechActual =  date('Y-m-d');
              $repository = $this->getDoctrine()->getRepository(Consecutivo::class);
              $registros = $repository->findByActivo(true);

              $fechaAnterior = $registros[0]->fechaactual;
              $fechaAnterior =  $fechaAnterior->format('Y-m-d');
              if ($fechaAnterior != $fechActual) {
                  $fecha = DateTime::createFromFormat('Y-m-d', $fechaAnterior);
                  $fecha->modify('+1 day');
                  $nuevaFecha = $fecha->format('Y-m-d');
                  $prefijo =  $fecha->format('Ymd');
                  $em = $this->getDoctrine()->getManager();
                  $repository = $this->getDoctrine()->getRepository(Consecutivo::class);
                  $consecutivo = $repository->findOneBy(['id' => 1]);
                  $consecutivo->setPrefijo($prefijo);
                  $consecutivo->setFechaactual(new \DateTime($nuevaFecha));
                  $consecutivo->setConsecutivo(0);
                  $em->flush();

                  $response = array(
                  'status' => 'success',
                  'code' => '400',
                  'msg' => 'Consecutivo actualizado',
                  
                  );
                return $this->handleView($this->view($response));
              }
              } catch (\Exception $e) {
                $response = array(
                  'status' => 'false',
                  'code' => '200',
                  'msg' => 'Error al actulizar consecutivo',
                  'data' =>  $e->getMessage()
                );
                return $this->handleView($this->view($response));
              }              
          
  }
}