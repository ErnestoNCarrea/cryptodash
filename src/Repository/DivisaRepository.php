<?php

namespace App\Repository;

use App\Entity\Divisa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Divisa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Divisa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Divisa[]    findAll()
 * @method Divisa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DivisaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Divisa::class);
    }

    // /**
    //  * @return Divisa[] Returns an array of Divisa objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Divisa
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
