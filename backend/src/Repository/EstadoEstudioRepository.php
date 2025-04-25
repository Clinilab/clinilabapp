<?php

namespace App\Repository;

use App\Entity\EstadoEstudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EstadoEstudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoEstudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoEstudio[]    findAll()
 * @method EstadoEstudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoEstudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoEstudio::class);
    }

    // /**
    //  * @return EstadoEstudio[] Returns an array of EstadoEstudio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EstadoEstudio
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
