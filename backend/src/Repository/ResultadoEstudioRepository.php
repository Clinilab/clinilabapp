<?php

namespace App\Repository;

use App\Entity\ResultadoEstudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResultadoEstudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultadoEstudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultadoEstudio[]    findAll()
 * @method ResultadoEstudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultadoEstudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultadoEstudio::class);
    }

    public function getAllNoTitle($idEstudio)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT re
            FROM App\Entity\ResultadoEstudio re
            WHERE re.activo = true
            AND re.estudio = :idEstudio
            AND re.tipo != 'title'
            ORDER BY re.posicion ASC";
        $consulta = $em->createQuery($dql);
        $consulta->setParameters(array(
            'idEstudio' => $idEstudio,
        ));
    
        return $consulta->getResult();
    }

    // /**
    //  * @return ResultadoEstudio[] Returns an array of ResultadoEstudio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResultadoEstudio
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
