<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use App\Entity\UserJobMeta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method UserJobMeta|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJobMeta|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJobMeta[]    findAll()
 * @method UserJobMeta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJobMetaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJobMeta::class);
    }

    /**
     * @param User $user
     * @param Job $job
     * @return mixed
     * @throws NonUniqueResultException
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


    /**
     * @param User $user
     * @return mixed
     */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('userJobMetadata')
            ->where('userJobMetadata.user =:user')
            ->setParameter('user',$user)
            ->getQuery()
            ->getResult();
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
