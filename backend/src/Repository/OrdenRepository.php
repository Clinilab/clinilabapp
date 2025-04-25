<?php

namespace App\Repository;

use App\Entity\Orden;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Orden|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orden|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orden[]    findAll()
 * @method Orden[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orden::class);
    }

    public function findByFecha($fechaInicio,$fechaFin)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT o
            FROM App\Entity\Orden o
            WHERE o.fecha BETWEEN :fechaInicio AND :fechaFin
            ";
        $consulta = $em->createQuery($dql);
        $consulta->setParameters(array(
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ));
    
        return $consulta->getResult();
    }

    public function findByUltimo()
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT MAX(o.id)
            FROM App\Entity\Orden o WHERE o.activo = true";
        $consulta = $em->createQuery($dql); 
    
        return $consulta->getOneOrNullResult();
    }

    public function searchOneLastOrFisrt($orderBy)
    { 
        $em = $this->getEntityManager();

        $dql = "SELECT o FROM App\Entity\Orden o WHERE o.activo = true ORDER BY o.id ".$orderBy;
        $consulta = $em->createQuery($dql)->setMaxResults(1);
    
        return $consulta->getOneOrNullResult();
    }
}
