<?php

namespace App\Repository;

use App\Entity\UsuarioExchange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsuarioExchange|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioExchange|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioExchange[]    findAll()
 * @method UsuarioExchange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioExchangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioExchange::class);
    }
}
