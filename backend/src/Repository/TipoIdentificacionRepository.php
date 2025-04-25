<?php

namespace App\Repository;

use App\Entity\TipoIdentificacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TipoIdentificacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoIdentificacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoIdentificacion[]    findAll()
 * @method TipoIdentificacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoIdentificacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoIdentificacion::class);
    }

    // /**
    //  * @return TipoIdentificacion[] Returns an array of TipoIdentificacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipoIdentificacion
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
