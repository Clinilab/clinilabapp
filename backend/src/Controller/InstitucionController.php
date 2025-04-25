<?php

namespace App\Controller;

use App\Entity\Institucion;
use App\Form\InstitucionType;
use App\Repository\InstitucionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Institucion controller.
 * @Route("/api/institucion", name="api_")
 */
class InstitucionController extends FOSRestController
{
    /**
   * 
   * @Rest\Get("/")
   */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Institucion::class);

        $institucion = $repository->findOneByActivo(true);

        if ($institucion) {
            $response = array(
                'status' => 'success', 
                'code' => '200', 
                'message' => 'Datos de Institución registrados.', 
                'data' => $institucion
            );
        } else {
            $response = array(
                'status' => 'error', 
                'code' => '200', 
                'message' => 'Datos de institución no registrados.'
            );
        }
        

        return $this->handleView($this->view($response));
    }

    /**
   * Create Medico.
   * @Rest\Post("/new")
   */
    public function new(Request $request): Response
    {
        $institucion = new Institucion();
        $form = $this->createForm(InstitucionType::class, $institucion);
        $data = json_decode($request->get("data",null), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($request->files->get('logo')) {
                $file = $request->files->get('logo');
                $extension = $file->guessExtension(); 
                $filename = "logo_".$data['identificacion'].'.'.$extension;
                $dir=__DIR__."/../../public/img/logos";

                $file->move($dir,$filename);
                $institucion->setLogo($filename);
            }
            
            $institucion->setActivo(true);

            $entityManager->persist($institucion);
            $entityManager->flush();

            $response = array(
                'status' => 'success', 
                'code' => '200', 
                'message' => 'Datos de institución registrados satisfactoriamente.', 
            );

            return $this->handleView($this->view($response, Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
   * Show Institucion.
   * @Rest\Get("/{id}/show")
   * 
   * @return Response
   */
    public function show(Institucion $institucion)
    {
        if ($institucion) {
            $response = array(
                'status' => 'success', 
                'code' => '200', 
                'message' => 'Registro encontrado satisfactoriamente.',
                'data' => $institucion
            );
        } else {
            $response = array(
                'status' => 'error', 
                'code' => '400', 
                'message' => 'Registro no encontrado.'
            );
        }
        
        return $response;
    }

    /**
   * Edit Institucion.
   * @Rest\Post("/{id}/edit")
   * 
   * @return Response
   */
    public function edit(Request $request, Institucion $institucion)
    {
        $form = $this->createForm(InstitucionType::class, $institucion);
        $data = json_decode($request->get("data",null), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if ($request->files->get('logo')) {
                $file = $request->files->get('logo');
                $extension = $file->guessExtension(); 
                $filename = "logo_".$data['identificacion'].'.'.$extension;
                $dir=__DIR__."/../../public/img/logos";

                $file->move($dir,$filename);
                $institucion->setLogo($filename);
            }

            $entityManager->flush();

            $response = array(
                'status' => 'success', 
                'code' => '200', 
                'message' => 'Datos de Institución actualizados satisfactoriamente.', 
            );

            return $this->handleView($this->view($response, Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
   * 
   * @Rest\Get("/{id}/delete")
   *
   */
    public function delete(Request $request, Institucion $institucion): Response
    {
        if ($this->isCsrfTokenValid('delete'.$institucion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($institucion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('institucion_index');
    }
}
