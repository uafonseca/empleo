<?php

namespace App\Repository;

use App\Entity\PaymentForJobsMetadata;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PaymentForJobsMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForJobsMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForJobsMetadata[]    findAll()
 * @method PaymentForJobsMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForJobsMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentForJobsMetadata::class);
    }

    /**
     * @param User $user
     * @param bool $active
     * @return mixed
     */
    public function checkUser(User $user, $active = true)
    {
        return $this->createQueryBuilder('paymentForJobsMetadata')
            ->where('paymentForJobsMetadata.user =:user')
            ->andWhere('paymentForJobsMetadata.active =:value')
            ->setParameter('user',$user)
            ->setParameter('value',$active)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function checkFreePack(User $user)
    {
        return $this->createQueryBuilder('pfjm')
            ->leftJoin('pfjm.package','package')
            ->andWhere('package.price = 0')
            ->andWhere('pfjm.user =:user')
            ->setParameter('user',$user)
            ->orderBy('pfjm.datePurchase')
            ->getQuery()
            ->getResult();
    }




    // /**
    //  * @return PaymentForJobsMetadata[] Returns an array of PaymentForJobsMetadata objects
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
    public function findOneBySomeField($value): ?PaymentForJobsMetadata
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
