<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Orden;

/**
 * Repositorio para la clase Orden.
 * 
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
}
