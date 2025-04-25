<?php

namespace App\Repository;

use App\Entity\ParametroEstudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ParametroEstudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParametroEstudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParametroEstudio[]    findAll()
 * @method ParametroEstudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametroEstudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParametroEstudio::class);
    }

    // /**
    //  * @return ParametroEstudio[] Returns an array of ParametroEstudio objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ParametroEstudio
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
