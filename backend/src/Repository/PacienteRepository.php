<?php

namespace App\Repository;

use App\Entity\Paciente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Paciente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paciente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paciente[]    findAll()
 * @method Paciente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PacienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paciente::class);
    }

    // /**
    //  * @return Paciente[] Returns an array of Paciente objects
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
    public function findOneBySomeField($value): ?Paciente
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function searchByFilters($filters)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT p
            FROM App\Entity\Paciente p, App\Entity\User u,
            WHERE p.user = u.id AND
            (u.nombres LIKE :filters OR u.apellidos LIKE :filters OR u.identificacion LIKE :filters)";
        $consulta = $em->createQuery($dql);
        $consulta->setParameters(array(
            'fechaInicio' => '%'.$filters.'%',
        ));
    
        return $consulta->getResult();
    }
}
