<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Paciente;
use App\Entity\Orden;
use App\Form\PacienteType;
use App\Entity\User;
use App\Entity\TipoIdentificacion;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * Paciente controller.
 * @Route("/api/paciente", name="api_")
 */
class PacienteController extends FOSRestController
{
  /**
   * 
   * @Rest\Get("/")
   *
   * @return Response
   */
  public function getPacienteAction()
  {
    $repository = $this->getDoctrine()->getRepository(Paciente::class);
    
    $registros = $repository->findByActivo(true);

    foreach ($registros as $key => $registro) {
      $registrosArray[] = array(
        'id' => $registro->getId(),
        'user' => $registro->getUser(),
        'direccion' => $registro->getDireccion(),
        'notas' => $registro->getNotas(),
        'foto' => $registro->getFoto(),
        'fechaNacimiento' => $registro->getFechaNacimiento(),
        'anio' => $registro->getFechaNacimiento()->format('Y'),
        'mes' => $registro->getFechaNacimiento()->format('m'),
        'dia' => $registro->getFechaNacimiento()->format('d'),
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
   * Create Paciente.
   * @Rest\Post("/new")
   *
   * @return Response
   */
  public function postPacienteAction(Request $request,UserPasswordEncoderInterface $encoder)
  {
    $paciente = new Paciente();

    $form = $this->createForm(PacienteType::class, $paciente);

    $data = json_decode($request->getContent(), true);
   
    $form->submit($data['paciente']);

    if ($form->isSubmitted() && $form->isValid()) {
      $em = $this->getDoctrine()->getManager();

      $repository = $this->getDoctrine()->getRepository(User::class);
      $userBd = $repository->findOneByIdentificacion($data['user']['identificacion']);

      if ($userBd) {
        $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'La Identificación ya se encuentra registrada', 
        );

        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }

      $userBd = $repository->findOneByEmail($data['user']['email']);

      if ($userBd) {
        $response = array(
            'status' => 'success', 
            'code' => '400', 
            'msg' => 'El correo electrónico ya esta registrado', 
        );

        return $this->handleView($this->view($response, Response::HTTP_CREATED));
      }

      
      $user = new User();
      
      $repositoryTipoIdentificacion = $this->getDoctrine()->getRepository(TipoIdentificacion::class);
      $tipoIdentificacion = $repositoryTipoIdentificacion->find($data['user']['tipoIdentificacion']);
      $user->setTipoIdentificacion($tipoIdentificacion);

      $plainPassword = $data['user']['identificacion'];
      $encoded = $encoder->encodePassword($user, $plainPassword);
      $user->setPassword($encoded);

      $user->setNombres($data['user']['nombres']);
      $user->setApellidos($data['user']['apellidos']);
      $user->setIdentificacion($data['user']['identificacion']);
      $user->setGenero($data['user']['genero']);
      $user->setEmail($data['user']['email']);
      $user->setTelefono($data['user']['telefono']);
      $user->setUsername($data['user']['email']);
      $user->setEnabled(1);
      $user->addRole("ROLE_PACIENTE");
      $em->persist($user);
      
      $em->flush();

      $paciente->setUser($user);
      $paciente->setActivo(true);

      $em->persist($paciente);
      $em->flush();

      $response = array(
          'status' => 'success', 
          'code' => '200', 
          'data' => $paciente, 
          'msg' => 'Paciente registrado satisfactoriamente.', 
      );

      return $this->handleView($this->view($response, Response::HTTP_CREATED));
    }
    return $this->handleView($this->view($form->getErrors()));
  }

   /**
   * Create Paciente.
   * @Rest\Post("/{id}/edit")
   *
   * @return Response
   */
  public function postEditPacienteAction(Request $request,Paciente $paciente,UserPasswordEncoderInterface $encoder)
  {
    try
    {    
        $form = $this->createForm(PacienteType::class, $paciente);
        $data = json_decode($request->getContent(), true);
        $form->submit($data['paciente']);

        if ($form->isSubmitted() && $form->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $conn = $this->getDoctrine()->getConnection();                   

          $repositoryTipoIdentificacion = $this->getDoctrine()->getRepository(TipoIdentificacion::class);
          $tipoIdentificacion = $repositoryTipoIdentificacion->find($data['user']['tipoIdentificacion']);         

        
          
         $sql = 'SELECT id, tipo_identificacion_id, identificacion FROM fos_user WHERE id <> :id AND identificacion = :identificacion';
            $params = [
                'id' => $data['paciente']['user'],
                'identificacion' => $data['user']['identificacion'],
            ];

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $resultSet = $stmt->fetch();
                    
        
          if ($resultSet){
            $response = array(
              'status' => 'false', 
              'code' => 400, 
              'msg' => 'Numero de identificación ya existe',
              'data' => $paciente);
            return $this->handleView($this->view($response));            
          }
          

          $sql = 'UPDATE fos_user SET nombres = :nombres, apellidos = :apellidos,
                tipo_identificacion_id = :tipoIdentificacion,identificacion=:identificacion,genero=:genero,
                email=:email, telefono=:telefono,username=:username,enabled=:enabled    WHERE id = :id';
          $params = [              
              'nombres' => trim($data['user']['nombres']),
              'apellidos' => trim($data['user']['apellidos']),
              'tipoIdentificacion' => $tipoIdentificacion->getId(),
              'identificacion' => trim($data['user']['identificacion']),
              'genero' => trim($data['user']['genero']),              
              'email' => trim($data['user']['email']),
              'telefono' => trim($data['user']['telefono']),
              'username' => trim($data['user']['email']),              
              'enabled' => 1,
              'id'=>$data['paciente']['user']
          ];
          $conn->executeUpdate($sql, $params);          
          $paciente->setActivo(true);

          $em->flush();
          $response = array(
              'status' => 'success', 
              'code' => '200', 
              'msg' => 'Registro actualizado satisfactoriamente', 
              'data' => $paciente, 
              'ok'=> $resultSet,                        

          );
          return $this->handleView($this->view($response, Response::HTTP_CREATED));
       }else{
        $response = array(
          'status' => 'false', 
          'code' => '400', 
          'msg' => 'Datos incorrectos', 
          'data' => $paciente);
        return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
      }
    } catch (\Exception $e) {
      $response = [
          'status' => 'false',
          'code' => '400',
          'msg' => 'Datos incorrectos',
          'data' => $e->getMessage(),
      ];  
      return $this->handleView($this->view($response));
  } 
    
    
  }

  /**
   * Show Paciente.
   * @Rest\Get("/{id}/show")
   *
   * @return Response
   */
  public function getShowPacienteAction(Paciente $paciente)
  {
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro',
        'data' => $paciente
    );

    return $response;
  }

  /**
   * Show Paciente.
   * @Rest\Get("/show/by/identificacion/{identificacion}")
   *
   * @return Response
   */
  public function getShowPacienteByIdentificacionAction($identificacion)
  {
    $repository = $this->getDoctrine()->getRepository(User::class);
    $user = $repository->findOneByIdentificacion($identificacion);

    if ($user) {
      $repository = $this->getDoctrine()->getRepository(Paciente::class);
      $paciente = $repository->findOneByUser($user->getId());

      if ($paciente) { 
        $response = array(
          'id' => $paciente->getId(),
          'user' => $paciente->getUser(),
          'direccion' => $paciente->getDireccion(),
          'notas' => $paciente->getNotas(),
          'foto' => $paciente->getFoto(),
          'fechaNacimiento' => $paciente->getFechaNacimiento()->format('Y-m-d'),
          'anio' => $paciente->getFechaNacimiento()->format('Y'),
          'mes' => $paciente->getFechaNacimiento()->format('m'),
          'dia' => $paciente->getFechaNacimiento()->format('d'),
          'identificacion' =>  $identificacion
        );
  
        $response = array(
          'status' => 'success', 
          'code' => '200', 
          'msg' => 'Registro encontrado',
          'data' => $response
        );
      } else {
        $response = array(
          'status' => 'error', 
          'code' => '400', 
          'msg' => 'El paciente no existe.',
          'data' => array('identificacion' =>  $identificacion)
        );
      }
    }else {
      $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'El usuario no existe.',
        'data' => array('identificacion' =>  $identificacion)
      );
    }
    return $response;
  }


  /**
   * 
   * @Rest\Get("/{id}/delete")
   *
   * @return Response
   */
  public function getDeletePacienteAction(Paciente $paciente)
  {
    $em = $this->getDoctrine()->getManager();
    if($paciente->getActivo()){
      $paciente->setActivo(false);
    }else{
      $paciente->setActivo(true);
    }
    
    $em->flush();
    
    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Registro eliminado',
        'data' => $paciente
    );
    return $response;
  }

  /**
   * 
   * @Rest\Get("/select")
   *
   * @return Response
   */
  public function getPacienteSelectAction()
  {
    $repository = $this->getDoctrine()->getRepository(Paciente::class);
    $registros = $repository->findByActivo(true);
    $registrosArray = [];
    foreach ($registros as $key => $r) {
        $registrosArray[$key] = array(
            'id' => $r->getId(),
            'text' => $r->getUser()->getNombres() . ' ' . $r->getUser()->getApellidos() . '/' . $r->getUser()->getIdentificacion(), 
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
   * Search Paciente.
   * @Rest\Post("/search")
   *
   * @return Response
   */
  public function postSearchPacienteAction(Request $request)
  {
    $repository = $this->getDoctrine()->getRepository(Paciente::class);

    $data = json_decode($request->getContent(), true);

    $pacientes = $repository->searchByFilters($data['filter']);

    $response = array(
        'status' => 'success', 
        'code' => '400', 
        'msg' => 'Listado de registros', 
        'data' => $pacientes
    );

    return $this->handleView($this->view($response));
  }

  /**
   * Upload file pacientes.
   * @Rest\Post("/upload")
   *
   * @return Response
   */
  public function uploadPacienteAction(Request $request, UserPasswordEncoderInterface $encoder)
  {
    $file = $request->files->get('file');

    /*$response = $file->guessExtension();
    return $this->handleView($this->view($response));*/

    if ($file->guessExtension() == 'txt') {
      $file = fopen($file->getPathName(), "r" );

      $transacciones = array(
        'error' => [],
        'success' => []
      );
      $length = 9;
      $batchSize = 500;
      $rows = 0;
      $cols = 0;

      if ($file) {
        //Leo cada linea del archivo hasta un maximo de caracteres (0 sin limite)
        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(User::class);

        while ($data = fgets($file)) {
          $data = explode(";",$data);
          $cols = count($data);

          if ($cols == $length) {
            $paciente = new Paciente();

            if ($rows > 0) {
              //Busca si ya existe el usuario registrado con la identificación del archivo plano
              $userBd = $repository->findOneByIdentificacion($data[4]);
  
              if ($userBd) {
                $transacciones['error'][] = array(
                  'status' => 'error', 
                  'code' => '200', 
                  'message' => 'Línea '.$rows.': El usuario con la identificación que desea registrar ya existe!'
                );
              } else {
                $userManager = $this->get('fos_user.user_manager');
                $userBd = $userManager->findUserBy(array('email' => $data[5]));

                if ($userBd) {
                  $transacciones['error'][] = array(
                    'status' => 'error', 
                    'code' => '200', 
                    'message' => 'Línea '.$rows.': El usuario con el correo '.$data[5].' que desea registrar ya existe!'
                  );
                } else {

                  //Registra datos de usuario
                  $user = new User();
              
                  $repositoryTipoIdentificacion = $this->getDoctrine()->getRepository(TipoIdentificacion::class);
                  $tipoIdentificacion = $repositoryTipoIdentificacion->find($data[2]);
                  $user->setTipoIdentificacion($tipoIdentificacion);
  
                  $plainPassword = $data[3];
                  $encoded = $encoder->encodePassword($user, $plainPassword);
                  $user->setPassword($encoded);
  
                  $user->setNombres(strtoupper($data[0]));
                  $user->setApellidos(strtoupper($data[1]));
                  $user->setIdentificacion($data[3]);
                  $user->setGenero(strtoupper($data[4]));
                  $user->setEmail($data[5]);
                  $user->setTelefono($data[6]);
                  $user->setUsername($data[5]);
                  $user->setEnabled(1);
                  $user->addRole("ROLE_PACIENTE");
                  $em->persist($user);
                  
                  $em->flush();
  
                  //Registra datos del paciente
                  $paciente = new Paciente();
  
                  $paciente->setUser($user);
                  $paciente->setDireccion($data[7]);
                  //$fechaNacimiento = new \DateTime($data[8]);
                  $paciente->setFechaNacimiento($data[8]);
                  $paciente->setNotas('N/A');
                  $paciente->setActivo(true);
  
                  $em->persist($paciente);
                  $em->flush();
  
                  $transacciones['success'][] = array(
                    'status' => 'success', 
                    'code' => '400', 
                    'message' => 'Línea '.$rows.': Usuario '.$data[5].' registrado satisfactoriamente!'
                  );
                }
              }
            }

            $rows++;
            
          } else {
            $transacciones['error'][] = array(
              'status' => 'error', 
              'code' => '200', 
              'message' => "Error! Fila:(".$rows.") No cumple con la longitud del formato estandar.",
            );
          }
        }

        /*if ($rows == 0) {
              $response = array(
                'status' => 'warning',
                'code' => 400,
                'message' => "Atención! Recuerde que la primera fila solo se usara para los titulos; debe insertar al menos una segunda fila de datos.",
              );
            } else {*/
              $response = array(
                'status' => 'success', 
                'code' => '200', 
                'message' => 'Proceso de carga de archivo plano terminado satisfactoriamente.', 
                'data' => $transacciones, 
            );
          //}

        fclose($file);
      } else {
        $response = array(
            'status' => 'warning',
            'code' => 400,
            'message' => "No se pudo leer el archivo.", 
        );
      }
    } else {
      $response = array(
        'status' => 'warning', 
        'code' => '400', 
        'message' => 'El formato de archivo no es válido, por favor intente cargando un archivo ".CSV"'
      );
    }

    return $this->handleView($this->view($response));
  }
}