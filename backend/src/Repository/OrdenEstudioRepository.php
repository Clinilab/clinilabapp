<?php

namespace App\Repository;

use App\Entity\OrdenEstudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrdenEstudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdenEstudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdenEstudio[]    findAll()
 * @method OrdenEstudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenEstudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdenEstudio::class);
    }
    
    public function getByEstadoGroupByEstudio($idEstado)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT e.id AS idEstudio, e.nombre AS estudio, 
            ee.nombre AS estado, count(oe.id) AS total
            FROM App\Entity\OrdenEstudio oe, App\Entity\Estudio e, App\Entity\EstadoEstudio ee
            WHERE oe.activo = true
            AND oe.estadoEstudio = :idEstado
            AND oe.estudio = e.id
            AND oe.estadoEstudio = ee.id
            GROUP BY e.id";
        $consulta = $em->createQuery($dql);
        $consulta->setParameters(array(
            'idEstado' => $idEstado,
        ));
    
        return $consulta->getResult();
    }

    // /**
    //  * @return OrdenEstudio[] Returns an array of OrdenEstudio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrdenEstudio
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
