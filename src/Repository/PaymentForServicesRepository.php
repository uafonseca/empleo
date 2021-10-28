<?php

namespace App\Repository;

use App\Entity\PaymentForServices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentForServices|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForServices|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForServices[]    findAll()
 * @method PaymentForServices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForServicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentForServices::class);
    }

    // /**
    //  * @return PaymentForServices[] Returns an array of PaymentForServices objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentForServices
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}