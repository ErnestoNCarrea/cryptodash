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

    // /**
    //  * @return UsuarioExchange[] Returns an array of UsuarioExchange objects
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
    public function findOneBySomeField($value): ?UsuarioExchange
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
