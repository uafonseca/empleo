<?php

namespace App\Repository;

use App\Entity\ContactMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactMessage[]    findAll()
 * @method ContactMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }


    /**
     * @param User $candidate
     * @param User $logged
     * @return int|mixed|string
     */
    public function findByCandidate(User $candidate, User $logged)
    {
        return $this->createQueryBuilder('contactMessage')
            ->andWhere('contactMessage.creator =:creator')
            ->andWhere('contactMessage.destinatario=:candidate')
            ->setParameter('candidate', $candidate)
            ->setParameter('creator', $logged)
            ->orderBy('contactMessage.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return ContactMessage[] Returns an array of ContactMessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ContactMessage
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}