<?php

namespace App\Services;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;

define('MULTIPART_BOUNDARY', '----'.md5(time()));
define('EOL',"\r\n");// PHP_EOL cannot be used for emails we need the CRFL '\r\n'

class Mailer
{
    private $mailer;
    private $templating;
    private $session;
    private $url;
    private $authorization;
    protected $em;

    function __construct( \Swift_Mailer $mailer, EngineInterface $templating, Session $session, EntityManager $em) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->session = $session;
        $this->em = $em;
        // URL to the API that sends the email.
        $this->url = 'https://107.20.199.106/email/1/send';
        $this->authorization  = 'c2FucGVkcm9zOkNvbG9tYmlhMjAxOA==';
    }

    /* ===================================== */
    public function sendEmail($subject,$to,$data,$plantilla,$from){
      try{
        $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($from)
        ->setTo($to)
        ->setBody(
            $this->templating->render(
                $plantilla,
                $data
            ), 'text/html'
        )
        ->setPriority(2);

        if(!$this->mailer->send($message, $failures)){
          $response = array(
            'code' => 200,
            'status' => 'warning',
            'message' => 'Mensajes enviados pero con '.count($failures).' excepciones',
            'failures' => $failures
          );
        }else{
          $response = array(
            'code' => 200,
            'status' => 'success',
            'message' => 'Mensajes enviados exitosamente!',
          );
        }
        //$response = $this->mailer->send($message);
      }catch(\Swift_TransportException $e){
        $response = array(
          'code' => 400,
          'status' => 'error',
          'message' => $e->getMessage(),
        );
      }catch (Exception $e) {
        $response = array(
          'code' => 400,
          'status' => 'error',
          'message' => $e->getMessage(),
        );
      }

      if($response['code'] == 200){
        if ($response['status'] == 'warning') {
          $this->session->getFlashBag()->set('info', $response['message']);
          if (count($response['failures']) > 0) {
            foreach ($$response['failures'] as $key => $failure) {
              $this->session->getFlashBag()->set('error', "Error en el correo: ".$failure);
            }
          }
        }else{
          $this->session->getFlashBag()->set('info', $response['message']);
        }
      }else{
        $this->session->getFlashBag()->set('error', "Se ha presentado un error al enviar correos: ".$response['message']);
      }

      return $response;
    }

    /**
     *
     * Valida un email usando filter_var y comprobar las DNS. 
     *  Devuelve true si es correcto o false en caso contrario
     *
     * param    string  $str la dirección a validar
     * return   boolean
     *
     */
    function is_valid_email($str)
    {
      $result = (false !== filter_var($str, FILTER_VALIDATE_EMAIL));
      
      if ($result)
      {
        list($user, $domain) = explode('@', $str);
        $result = checkdnsrr($domain, 'MX');
      }
      
      return $result;
    }

    public function templateNotify($trazabilidad, $to){
      $subject = "Confirmación PQRSF";
      $data = array('entity'=>$trazabilidad);
      $plantilla = 'JHWEBPqrsfBundle:default:email.notify.html.twig';
      $from = 'no_responder@apps.hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);
      
      return $response;
    }

    public function templateResponse($trazabilidad){
      $subject = "Confirmación de Respuesta PQRSF";
      $data = array('entity'=>$trazabilidad);
      $plantilla = 'JHWEBPqrsfBundle:default:email.response.html.twig';
      $from = 'no_responder@apps.hospitalsanpedro.org';
      $to[] = 'pqrsfhospitalsanpedro.org@gmail.com';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateAssignment($trazabilidad, $to){
      $subject = "Asignación PQRSF";
      $data = array('entity'=>$trazabilidad);
      $plantilla = 'JHWEBPqrsfBundle:default:email.assignment.html.twig';
      $from = 'no_responder@apps.hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);
      
      return $response;
    }

    public function templateSalvavidasCampania($donante, $campania){
      //E-Mail parameters
      $subject = "Notificación Banco de Sangre";
      $to = $donante->getCorreo();
      $data = array('donante' => $donante, 'campania' => $campania);
      $plantilla = 'JHWEBSalvavidasBundle:default:email.campania.html.twig';
      $from = 'bancodesangrepi@fhostalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateSalvavidasWelcome($donante, $campania){
      //E-Mail parameters
      $subject = "Notificación Banco de Sangre";
      $to = $donante->getCorreo();
      $data = array('donante' => $donante, 'campania' => $campania);
      $plantilla = 'JHWEBSalvavidasBundle:default:email.welcome.html.twig';
      $from = 'bancodesangre@hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateSalvavidasCron($correos, $campania){
      //E-Mail parameters
      $subject = "Notificación Banco de Sangre";
      $to = $correos;
      $data = array('campania' => $campania);
      $plantilla = 'JHWEBSalvavidasBundle:default:email.cron.html.twig';
      $from = 'bancodesangre@hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateSalvavidasNext($donante, $campania){
      //E-Mail parameters
      $subject = "Notificación Banco de Sangre";
      $to = $donante->getCorreo();
      $data = array('donante' => $donante, 'campania' => $campania);
      $plantilla = 'JHWEBSalvavidasBundle:default:email.next.html.twig';
      $from = 'bancodesangre@hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateTrazabilidad($trazabilidad){
      //Valida el envio de correos al acudiente
      if ($trazabilidad->getSolicitud()->getAcudiente()->getCorreos() && $trazabilidad->getEstado()->getRespuesta() != null){
        $to = array();

        foreach ($trazabilidad->getSolicitud()->getAcudiente()->getCorreos() as $key => $correo) {
          array_push($to,$correo);
        };

        //Envia la notificación de la nueva trazabilidad al acudiente
        $this->templateNotify($trazabilidad, $to);
        //Envia la confirmación de notificaciones al administrador
        $this->templateResponse($trazabilidad);
      }

      //Valida el envio de correos a los coordinadores correspondientes
      if ($trazabilidad->getFuncionario()->getUsuario()->getCorreos() && $trazabilidad->getFuncionario()->getUsuario()->getRole() != 'ROLE_ADMIN') {
        //E-Mail parameters
        $to = array();

        foreach ($trazabilidad->getFuncionario()->getUsuario()->getCorreos() as $key => $correo) {
          array_push($to,$correo);
        };

        //Envia el mensaje de la nueva asignación al coordinador
        $this->templateAssignment($trazabilidad, $to);
      }
    }

    public function Actividad($solicitante){
      $subject = "Registro de proceso de referenciación";
      $data = array('entity'=>$solicitante);
      $plantilla = 'JHWEBReferenciacionBundle:default:email.register.html.twig';
      $to = $solicitante->getContactoCorreo();
      $from = 'no_responder@apps.hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);
      
      return $response;
    } 

    public function templateReferenciacionRegister($correos, $solicitante){
      //E-Mail parameters
      $subject = "Notificación registro solicitante";
      $to = $correos;
      $data = array('entity'=>$solicitante);
      $plantilla = 'JHWEBReferenciacionBundle:default:email.register.html.twig';
      $from = 'plancalidad@hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    } 

    public function templateReferenciacionActividad($correos, $mensaje, $actividad){
      //E-Mail parameters
      $subject = "Notificación proceso de Referenciación";
      $to = $correos;
      $data = array('message'=> $mensaje, 'actividad'=>$actividad);
      $plantilla = 'JHWEBReferenciacionBundle:default:email.activity.html.twig';
      $from = 'plancalidad@hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templateReferenciacionEncuestaActividad($integrantes, $idActividad){
      
      foreach ($integrantes as $key => $integrante) {
        //E-Mail parameters
        $subject = "Encuesta satisfacción proceso de Referenciación";
        $to = $integrante->getCorreo();

        $data = array('integrante'=> $integrante, 'idActividad'=> $idActividad);
        $plantilla = 'JHWEBReferenciacionBundle:default:email.encuesta.html.twig';
        $from = 'plancalidad@hospitalsanpedro.org';
        //Envio de E-Mail
        $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);
        
        return $response;
      }
    }

    public function templateTestEmail($correos, $mensaje){
      //E-Mail parameters
      $subject = "Mensaje de prueba";
      $to = $correos;
      $data = array('message'=> $mensaje);
      $plantilla = 'JHWEBUserBundle:default:email.test.html.twig';
      $from = 'no_responder@apps.hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }

    public function templatePasswordRecovery($usuario, $mensaje){
      //E-Mail parameters
      $subject = "Recuperación de contraseña";
      $to = $usuario->getCorreos()[0];
      $data = array('message'=> $mensaje, 'usuario' => $usuario);
      $plantilla = 'JHWEBUserBundle:default:email.recovery.html.twig';
      $from = 'no_responder@apps.hospitalsanpedro.org';
      //Envio de E-Mail
      $response = $this->sendEmail($subject,$to,$data,$plantilla,$from);

      return $response;
    }
}
