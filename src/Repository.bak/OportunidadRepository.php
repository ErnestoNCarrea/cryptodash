<?php

namespace App\Repository;

use App\Entity\Oportunidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * @method Oportunidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oportunidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oportunidad[]    findAll()
 * @method Oportunidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OportunidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oportunidad::class);
    }
}
