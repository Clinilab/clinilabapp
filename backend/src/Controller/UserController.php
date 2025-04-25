<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\User;
use App\Form\UserType;
/**
 * User controller.
 * @Route("/api/user", name="api_")
 */
class UserController extends FOSRestController
{
  /**
   * listado de usuarios por conjunto residencial.
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getUserAction()
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
    $usuarios = $repository->findOneBySomeField();

    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Listado de usuarios', 
        'data' => $usuarios
    );
    return $this->handleView($this->view($response));
  }

  /**
   * Create User.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postUserAction(Request $request,UserPasswordEncoderInterface $encoder)
  {
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $data = json_decode($request->get("data",null), true);
   
    $form->submit($data);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $repository = $this->getDoctrine()->getRepository(User::class);
      $userBd = $repository->findOneByIdentificacion($data['identificacion']);
      if ($userBd) {
        $response = array(
            'status' => 'success', 
            'code' => '200', 
            'message' => 'la Identificación ya se encuentra registrada', 
        );
        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      } 

      $userBd = $repository->findOneByEmail($data['email']);
      if ($userBd) {
        $response = array(
            'status' => 'success', 
            'code' => '200', 
            'message' => 'El correo electrónico ya esta registrado', 
        );
        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }

      $userBd = $repository->findOneByUsername($data['username']);
      if ($userBd) {
        $response = array(
            'status' => 'success', 
            'code' => '200', 
            'message' => 'El usuario ya esta registrado', 
        );
        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }

      $fileHeader = $request->files->get('file');

      if ($fileHeader) {
        $extension = $fileHeader->guessExtension(); 
        $filename = "hoja_trabajo_".$data['identificacion'].'.'.$extension;
        $dir=__DIR__."/../../public/img/firmas";

        $fileHeader->move($dir,$filename);
        $user->setFirma($filename);
      } 

      $plainPassword = $user->getPassword();
      $encoded = $encoder->encodePassword($user, $plainPassword);
      $user->setPassword($encoded);
      $em->persist($user);
      $em->flush();
      $user->setEnabled(true);
      $em->flush(); 
      $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'Usuario creado', 
        );
      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Edit User.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditUserAction(Request $request, User $user, UserPasswordEncoderInterface $encoder)
  {
    $oldPassword = $user->getPassword();

    $form = $this->createForm(UserType::class, $user);

    $data = json_decode($request->get("data",null), true);

    $form->submit($data);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $fileHeader = $request->files->get('file');
    
      if ($fileHeader) {
        $extension = $fileHeader->guessExtension(); 
        $filename = "hoja_trabajo_".$data['identificacion'].date('is').'.'.$extension;
        $dir=__DIR__."/../../public/img/firmas";

        $fileHeader->move($dir,$filename);
        $user->setFirma($filename);
      }

      if ($data['password']) {
        $user->setPassword($data['password']);
        $plainPassword = $user->getPassword();
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
      } else {
        $user->setPassword($oldPassword);
      }

      $em->flush();

      $response = array(
          'status' => 'success', 
          'code' => '400', 
          'msg' => 'Usuario actualizado satisfactoriamente.'
      );
      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

  /**
   * Show user.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowUserAction(User $user)
  {
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'User',
        'data' => $user
    );
    return $response;
  }

  /**
   * Show user.
   * @Rest\Post("/get/by/username")
   *
   * @return Response
   */
  public function postUserByUsernameAction(Request $request)
  {
    $data = json_decode($request->getContent(), true);
   
    $repository = $this->getDoctrine()->getRepository(User::class);
    $user = $repository->findOneByUsername($data["username"]);
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'User',
        'data' => $user
    );
    return $response;
  }

  /**
   * Show conjunto residencial.
   * @Rest\Get("/{id}/delete")
   *
   * @return Response
   */
  public function getDeleteUserAction(User $user)
  {
    $em = $this->getDoctrine()->getManager();

    if($user->isEnabled()){
      $user->setEnabled(false);
    }else{
      $user->setEnabled(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'User eliminado',
        'data' => $user
    );
    
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getUserSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
    $registros = $repository->findByEnabled(true);
    $registrosArray =null;
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getIdentificacion()." ".$r->getNombres()." ".$r->getApellidos(), 
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