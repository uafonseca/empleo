<?php

namespace App\Repository;

use App\Entity\Calification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Calification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calification[]    findAll()
 * @method Calification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calification::class);
    }

    // /**
    //  * @return Calification[] Returns an array of Calification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Calification
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
