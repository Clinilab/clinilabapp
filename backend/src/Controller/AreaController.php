<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Area;

/**
 * area controller.
 * @Route("/api/area", name="api_")
 */
class AreaController extends FOSRestController
{
    /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getEstudioAction()
  {
    $repository = $this->getDoctrine()->getRepository(Area::class);
    $registros = $repository->findAll(true);
    $registrosArray= null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getAreaNombre(), 
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
