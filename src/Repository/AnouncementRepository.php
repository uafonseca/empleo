<?php

namespace App\Repository;

use App\constants;
use App\Entity\Anouncement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Anouncement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anouncement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anouncement[]    findAll()
 * @method Anouncement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnouncementRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Anouncement::class);
    }
	public function searchByFilters($profesion,
		$location,
		$gender,
		$experience){
		$qb = $this->createQueryBuilder('j');
		if ($profesion != null) {
			$qb->andWhere('j.profession =: key ')
				->setParameter('key', '%'.$profesion.'%');
		}
		if ($location != null) {
			$qb->orWhere('j.Location LIKE :key')
				->setParameter('key', '%'.$location.'%');
		}
		if ($gender != null) {
			$qb->orWhere('j.gender LIKE :key')
				->setParameter('key', '%'.$gender.'%');
		}
		if ($experience != null) {
			$qb->orWhere('j.experience LIKE :key')
				->setParameter('key', '%'.$experience.'%');
		}
		$qb->andWhere('j.status = :status')
			->setParameter('status', constants::JOB_STATUS_ACTIVE);
		
		return $qb
			->orderBy('j.date', 'DESC')
			->getQuery()
			->getResult();
		
	}
    // /**
    //  * @return Anouncement[] Returns an array of Anouncement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Anouncement
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
