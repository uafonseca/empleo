<?php

namespace App\Repository;

use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentForServicesMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentForServicesMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentForServicesMetadata[]    findAll()
 * @method PaymentForServicesMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentForServicesMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentForServicesMetadata::class);
    }

    /**
     * @param User $user
     * @param bool $active
     * @return mixed
     */
    public function checkUser(User $user, $active = true)
    {
        return $this->createQueryBuilder('pfsm')
            ->where('pfsm.user =:user')
            ->andWhere('pfsm.active =:value')
            ->setParameter('user',$user)
            ->setParameter('value',$active)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return PaymentForServicesMetadata[] Returns an array of PaymentForServicesMetadata objects
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
    public function findOneBySomeField($value): ?PaymentForServicesMetadata
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
