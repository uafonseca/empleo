<?php

namespace App\Repository;

use App\constants;
use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)

 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Job::class);
    }
    public function findAllHome()
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.expiredDate >= :val')
            ->setParameter('val', new \DateTime())
            ->orderBy('j.expiredDate', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
    public function countJob($user){
        return count($this->createQueryBuilder('j')
            ->andWhere('j.user = :user')
            ->setParameter('user',$user)
            ->getQuery()
            ->getResult());
    }
    public function Locations(){
        return $this->createQueryBuilder('j')
            ->select('j.localtion')
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }
    public function search($keywords , $location){
        $qb = $this->createQueryBuilder('j');
        if($keywords){
            $qb->andWhere('j.title LIKE :key OR j.description LIKE :key')
                ->setParameter('key', '%' . $keywords . '%')
                ;
        }
        if($location){
            $qb->orWhere('j.your_localtion LIKE :key OR j.localtion LIKE :key')
                ->setParameter('key', '%' . $keywords . '%')
            ;
        }
        return $qb
            ->orderBy('j.expiredDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function expired(){
        return $this->createQueryBuilder('j')
            ->update()
            ->set('j.status', '?1')
            ->setParameter(1,constants::JOB_STATUS_EXPIRED)
            ->where('j.expiredDate < ?2')
            ->setParameter(2, new \DateTime())
            ->andWhere('j.status != ?3')
            ->setParameter(3, constants::JOB_STATUS_EXPIRED)
            ->getQuery()->getSingleScalarResult();
    }

     /**
      * @return Job[] Returns an array of Job objects
      */

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function finJobByUser($user){
        return $this->createQueryBuilder('j')
            ->andWhere('j.user = :val')
            ->setParameter('val', $user)
            ->orderBy('j.status', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findOneBySomeField($value): ?Job
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
