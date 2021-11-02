<?php

/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 02/03/2019
 * Time: 21:41
 */

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    /**
     * @return User[] Returns an array of user objects
     */
    public function findByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @param Job $job
     * @return mixed
     */
    public function findCandidates(User $user, Job $job)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.userJobMetadata', 'userJobMetadata')
            ->where('userJobMetadata.job =:job')
            ->andWhere('userJobMetadata.user =:user')
            ->setParameter('job', $job)
            ->setParameter('user', $user)
            ->orderBy('userJobMetadata.status')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of user objects
     */
    public function findCandidatesList($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.jobAppiled', 'uj')
            ->join('App\Entity\Job', 'job')
            ->where('job.id =:id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findUsersByJobsAppiled(array $jobs = [])
    {
        return $this->createQueryBuilder('user')
            ->join('user.jobAppiled', 'appiled')
            ->where('appiled IN (:list)')
            ->setParameter('list', $jobs)
            ->getQuery()
            ->getResult();
    }
}
