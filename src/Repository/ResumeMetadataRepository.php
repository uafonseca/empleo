<?php

namespace App\Repository;

use App\Entity\ResumeMetadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ResumeMetadata|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResumeMetadata|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResumeMetadata[]    findAll()
 * @method ResumeMetadata[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResumeMetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResumeMetadata::class);
    }

    // /**
    //  * @return ResumeMetadata[] Returns an array of ResumeMetadata objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ResumeMetadata
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}