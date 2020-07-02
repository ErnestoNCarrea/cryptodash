<?php

namespace App\Repository;

use App\Entity\Oportunidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    // /**
    //  * @return Oportunidad[] Returns an array of Oportunidad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Oportunidad
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
