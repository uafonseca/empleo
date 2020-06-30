<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use App\Entity\UserJobMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserJobMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJobMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJobMetadata[]    findAll()
 * @method UserJobMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJobMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJobMetadata::class);
    }

    /**
     * @param User $user
     * @param Job $job
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByUserJob(User $user, Job $job)
    {
        return $this->createQueryBuilder('userJobMetadata')
            ->where('userJobMetadata.user =:user')
            ->andwhere('userJobMetadata.job =:job')
            ->setParameter('user',$user)
            ->setParameter('job',$job)
            ->getQuery()
            ->getOneOrNullResult();
    }
    // /**
    //  * @return UserJobMetadata[] Returns an array of UserJobMetadata objects
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
    public function findOneBySomeField($value): ?UserJobMetadata
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
