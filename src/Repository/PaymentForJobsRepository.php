<?php

namespace App\Repository;

use App\Entity\PaymentForJobs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentForJobs|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForJobs|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForJobs[]    findAll()
 * @method PaymentForJobs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForJobsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentForJobs::class);
    }

    // /**
    //  * @return PaymentForJobs[] Returns an array of PaymentForJobs objects
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
    public function findOneBySomeField($value): ?PaymentForJobs
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
