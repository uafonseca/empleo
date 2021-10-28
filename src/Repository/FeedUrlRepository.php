<?php

namespace App\Repository;

use App\Entity\FeedUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FeedUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method FeedUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method FeedUrl[]    findAll()
 * @method FeedUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedUrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeedUrl::class);
    }

    /**
     * @return FeedUrl[] Returns an array of FeedUrl objects
     */

    public function findByEnabledField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.enabled = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }



    public function findOneBySomeField($value): ?FeedUrl
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}