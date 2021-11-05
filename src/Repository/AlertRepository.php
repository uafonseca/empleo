<?php

namespace App\Repository;

use App\Entity\Alert;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getValidAlerts(){
        $emails = $this->createQueryBuilder('em')
            ->select('u.email')
            ->from(User::class,'u')
            ->getQuery()
            ->getResult();

        return $this->createQueryBuilder('alerts')
            ->where('alerts.email NOT IN (:emails)')
            ->setParameter('emails',$emails)
            ->getQuery()
            ->getResult();
    }

}