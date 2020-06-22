<?php

namespace App\Repository;

use App\Entity\Ocupation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ocupation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ocupation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ocupation[]    findAll()
 * @method Ocupation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OcupationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ocupation::class);
    }

    // /**
    //  * @return Ocupation[] Returns an array of Ocupation objects
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
    public function findOneBySomeField($value): ?Ocupation
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
