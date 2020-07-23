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

    // /**
    //  * @return UsuarioPar[] Returns an array of UsuarioPar objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UsuarioPar
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
