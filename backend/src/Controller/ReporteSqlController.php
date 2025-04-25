<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\ReportesSql;
use App\Entity\ResultadoEstudio;
use App\Entity\ValorResultado;
use App\Entity\OrdenEstudio;
use App\Form\ResultadoEstudioType;
/**
 * ReporteSql controller.
 * @Route("/api/reporte/sql", name="api_")
 */
class ReporteSqlController extends FOSRestController
{
    

  /**
   * 
   * @Rest\Get("/reporte")
   *
   * @return Response
   */
    public function selectConsultas()
    {
        $conn = $this->getDoctrine()->getConnection();

           $sql = "select  * from reportes_sql";    
          $stEstudios = $conn->prepare($sql);
          $resultEstudios = $stEstudios->executeQuery();
          $registros = $resultEstudios->fetchAllAssociative();

          $response = array(
            'status' => 'success', 
            'code' => 200, // Cambiar '400' a '200' para indicar una respuesta exitosa
            'msg' => 'Listado de registros', 
            'data' => $registros
        );
        return $this->handleView($this->view($response));

    }

  /** 
   * 
   * @Rest\Post("/reportefecha")
   *
   * @return Response
   */

  public function reportePorFechas(Request $request)
  {

    $conn = $this->getDoctrine()->getConnection();
    $data_response = json_decode($request->getContent(), true);

try {
    $sql = 'SELECT id, nombre, descripcion, consulta_sql, campos_sql, fecha_creacion FROM reportes_sql WHERE id = :id';
    $stConsulta = $conn->prepare($sql);
    $resultConsulta = $stConsulta->executeQuery(['id' => $data_response['id']]);
    $orden = $resultConsulta->fetchAssociative();

    if ($orden) {
        $consultaSql = $orden['consulta_sql'];
        $camposSql = $orden['campos_sql'];

        $sqlData = $consultaSql;
        $stData = $conn->prepare($sqlData);
        $resultData = $stData->executeQuery(['fecha_ini' => $data_response['fechaInicio'], 'fecha_fin' => $data_response['fechaFin']]);

        if ($resultData->rowCount() > 0) { // Comprobar si hay datos
            $csvData = $camposSql . "\n";
            foreach ($resultData as $data) {
                $csvData .= implode(",", $data) . "\n";
            }

            $filePath = './ReporteFecha.csv';
            file_put_contents($filePath, $csvData);

            $response = [
                'status' => 'success',
                'code' => '200',
                'msg' => 'Informe generado',
                'data' => $filePath,
            ];
        } else {
            $response = [
                'status' => 'false',
                'code' => '400',
                'msg' => 'No existen datos',
                'data' => null,
            ];
        }
    } else {
        $response = [
            'status' => 'false',
            'code' => '400',
            'msg' => 'No existe consulta',
            'data' => null,
        ];
    }

    return $this->handleView($this->view($response));
} catch (\Exception $e) {
    $response = [
        'status' => 'false',
        'code' => '400',
        'msg' => 'Error al generar la consulta',
        'data' => $e->getMessage(),
    ];

    return $this->handleView($this->view($response));
}
       
  }
}
