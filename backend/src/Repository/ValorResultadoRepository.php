<?php

namespace App\Repository;

use App\Entity\ValorResultado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ValorResultado|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValorResultado|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValorResultado[]    findAll()
 * @method ValorResultado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValorResultadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValorResultado::class);
    }

    public function findForChart($idResultadoEstudio, $idPaciente)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT vr.id, o.numero, re.nombre, vr.valor, oe.fechaValidacion, 
        um.nombre AS unidad_medida_nombre, um.simbolo As unidad_medida_simbolo
            FROM App\Entity\ValorResultado vr, App\Entity\ResultadoEstudio re, 
            App\Entity\OrdenEstudio oe, App\Entity\Orden o, App\Entity\UnidadMedida um
            WHERE re.id = vr.resultadoEstudio AND oe.id = vr.ordenEstudio 
            AND o.id = oe.orden AND o.paciente = :idPaciente 
            AND um.id = re.unidadMedida
            AND re.id = :idResultadoEstudio
            AND oe.estadoEstudio = 2
            AND oe.activo = true
            AND vr.valor != 0
            ORDER BY oe.fechaValidacion DESC";

        $consulta = $em->createQuery($dql);
        
        $consulta->setParameters(array(
            'idResultadoEstudio' => $idResultadoEstudio,
            'idPaciente' => $idPaciente,
        ));

        $consulta->setMaxResults(15);
    
        return $consulta->getResult();
    }
}
