<?php

namespace App\Repository;

use App\constants;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\TextUI\XmlConfiguration\Constant;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return void
     */
    public function orderByDate(User $user)
    {
        return $this->createQueryBuilder('notification')
            ->where('notification.user=:user')
            ->orderBy('notification.date', 'desc')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @param string $type
     * @param boolean $active
     * @return void
     */
    public function findByType(User $user, string $type, bool $active = true)
    {
        return $this->createQueryBuilder('notification')
            ->where('notification.user=:user')
            ->andWhere('notification.type = :t')
            ->andWhere('notification.active = :a')
            ->orderBy('notification.date', 'desc')
            ->setParameter('user', $user)
            ->setParameter('t', constants::RESPUESTA_CONSULTA_CREATE)
            ->setParameter('a', $active)
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
