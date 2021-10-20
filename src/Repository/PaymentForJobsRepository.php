<?php

namespace App\Repository;

use App\Entity\PaymentForJobs;
use App\Entity\User;
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

    //    public function  findAviables(User $user)
    //    {
    //        return $this->createQueryBuilder('pfj')
    //            ->leftJoin('pfj.users','user')
    //            ->where('')
    //
    //    }

    /**
     * @return PaymentForJobs[] Returns an array of PaymentForJobs objects
     */
    public function getThisMonth($month)
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.name,COUNT(p) as count')
            ->join('p.paymentForJobsMetadata', 'metadata')
            ->andWhere('MONTH(metadata.datePurchase) = :val')
            ->groupBy('p.id')
            ->setParameter('val', $month)
            ->getQuery()
            ->getResult();
    }

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