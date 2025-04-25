<?php

namespace App\Repository;

use App\Entity\Institucion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institucion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institucion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institucion[]    findAll()
 * @method Institucion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitucionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institucion::class);
    }

    // /**
    //  * @return Institucion[] Returns an array of Institucion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Institucion
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
