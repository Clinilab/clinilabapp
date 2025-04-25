<?php

namespace App\Repository;

use App\Entity\HojaTrabajo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method HojaTrabajo|null find($id, $lockMode = null, $lockVersion = null)
 * @method HojaTrabajo|null findOneBy(array $criteria, array $orderBy = null)
 * @method HojaTrabajo[]    findAll()
 * @method HojaTrabajo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HojaTrabajoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HojaTrabajo::class);
    }

    // /**
    //  * @return HojaTrabajo[] Returns an array of HojaTrabajo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HojaTrabajo
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
