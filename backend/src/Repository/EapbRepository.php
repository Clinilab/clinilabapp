<?php

namespace App\Repository;

use App\Entity\Eapb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Eapb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Eapb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Eapb[]    findAll()
 * @method Eapb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EapbRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Eapb::class);
    }

    // /**
    //  * @return Eapb[] Returns an array of Eapb objects
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
    public function findOneBySomeField($value): ?Eapb
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
