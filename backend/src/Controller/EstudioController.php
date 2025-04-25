<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Estudio;
use App\Form\EstudioType;
use PhpParser\Node\Stmt\TryCatch;

/**
 * Estudio controller.
 * @Route("/api/estudio", name="api_")
 */
class EstudioController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getEstudioAction()
  {
    $repository = $this->getDoctrine()->getRepository(Estudio::class);
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
   * Create Estudio.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postEstudioAction(Request $request)
  {
    $estudio = new Estudio();
    $form = $this->createForm(EstudioType::class, $estudio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $estudio->setActivo(true);
      if ($estudio->getEstudio()) {
        $estudio->setCodigo($estudio->getEstudio()->getCodigo() . $estudio->getCodigo());
      } else {
        $estudio->setCodigo($estudio->getCodigo());
      }


      try {
        $em->persist($estudio);

        $em->flush();
        $response = array(
          'status' => 'success',
          'code' => '400',
          'msg' => 'Registro creado',
        );
        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      } catch (\Exception $e) {
        $response = array(
          'status' => 'false',
          'code' => '500',
          'msg' => $e->getMessage(),

        );
        return $this->handleView($this->view($response, Response::HTTP_BAD_REQUEST));
      }
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create Estudio.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditEstudioAction(Request $request,Estudio $estudio)
  {
    $form = $this->createForm(EstudioType::class, $estudio);
    $data = json_decode($request->getContent(), true);
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($estudio);
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
   * Show Estudio.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowEstudioAction(Estudio $registro)
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
  public function getDeleteEstudioAction(Estudio $estudio)
  {
    $em = $this->getDoctrine()->getManager();
    if($estudio->getActivo()){
      $estudio->setActivo(false);
    }else{
      $estudio->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $estudio
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getEstudioSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Estudio::class);
    $registros = $repository->findByActivo(true);
    $registrosArray= null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getCodigo().'/'.$r->getNombre(), 
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