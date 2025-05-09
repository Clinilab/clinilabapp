<?php

namespace App\Repository;

use App\Entity\Consecutivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Consecutivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consecutivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consecutivo[]    findAll()
 * @method Consecutivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsecutivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consecutivo::class);
    }

    // /**
    //  * @return Consecutivo[] Returns an array of Consecutivo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Consecutivo
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
