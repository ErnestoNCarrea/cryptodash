<?php

namespace App\Repository;

use App\Entity\UsuarioPar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UsuarioPar|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioPar|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioPar[]    findAll()
 * @method UsuarioPar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioParRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioPar::class);
    }
}
