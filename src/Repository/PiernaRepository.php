<?php

namespace App\Repository;

use App\Entity\Pierna;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repositorio para la clase Pierna.
 * 
 * @method Pierna|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pierna|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pierna[]    findAll()
 * @method Pierna[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PiernaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pierna::class);
    }
}
