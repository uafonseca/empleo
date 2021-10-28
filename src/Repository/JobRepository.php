<?php

namespace App\Repository;

use App\constants;
use App\Entity\Job;
use function Deployer\add;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Utility\Graph\Month;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
	public function __construct(ManagerRegistry $registry)
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
			->getResult();
	}

	/**
	 * @param $key
	 * @return mixed
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getCountCategory($key)
	{
		return $this->createQueryBuilder('c')
			->select('count(c) as count')
			->innerJoin('c.category', 'category')
			->where('category.name LIKE :key')
			->setParameter('key', '%' . $key . '%')
			->getQuery()
			->getOneOrNullResult();
	}

	public function getCounter()
	{
		$ouput = [];
		foreach ($this->categories() as $key => $category) {
			$ouput[] = [
				[
					$category['id'],
					$category['name']
				],
				$this->createQueryBuilder('job')
					->select('COUNT(job.id) as cantidad')
					->where('job.category=:c')
					->setParameter('c', $category['id'])
					->getQuery()
					->getSingleResult()
			];
		}
		return $ouput;
	}

	public function categories()
	{
		return $this->createQueryBuilder('job')
			->select('category.id, category.name')
			->join('job.category', 'category')
			->distinct()
			->getQuery()
			->getResult();
	}

	public function countJob($user)
	{
		return count(
			$this->createQueryBuilder('j')
				->andWhere('j.user = :user')
				->setParameter('user', $user)
				->getQuery()
				->getArrayResult()
		);
	}

	public function getLocationsByName()
	{
		return $this->createQueryBuilder('j')
			->select('j.localtion as name')
			->distinct()
			->getQuery()
			->getResult();
	}

	public function getCityesByName()
	{
		return $this->createQueryBuilder('j')
			->select('j.city as name')
			->distinct()
			->getQuery()
			->getResult();
	}

	public function getCompanyByName()
	{
		return $this->createQueryBuilder('j')
			->select('j.company_name as name')
			->distinct()
			->getQuery()
			->getResult();
	}

	public function getCompanyCount($key)
	{
		return $this->createQueryBuilder('j')
			->select('count(j) as count')
			->where('j.company_name = :key')
			->setParameter('key', $key)
			->getQuery()
			->getOneOrNullResult();
	}

	public function getCount($key)
	{
		return $this->createQueryBuilder('j')
			->select('count(j) as count')
			->where('j.localtion = :key')
			->setParameter('key', $key)
			->getQuery()
			->getOneOrNullResult();
	}

	public function getCityCount($key)
	{
		return $this->createQueryBuilder('j')
			->select('count(j) as count')
			->where('j.city = :key')
			->setParameter('key', $key)
			->getQuery()
			->getOneOrNullResult();
	}

	public function listServices()
	{
		return $this->createQueryBuilder('j')
			->andWhere('j.status =:status')
			->setParameter('status', constants::JOB_STATUS_ACTIVE)
			->getQuery()
			->getResult();
	}

	public function Cityes()
	{
		return $this->createQueryBuilder('j')
			->select('j.city, count(j) as count')
			->distinct()
			->orderBy('count', 'ASC')
			->getQuery()
			->getResult();
	}

	public function Company()
	{
		return $this->createQueryBuilder('j')
			->select('j.company_name, count(j) as count')
			->distinct()
			->orderBy('count', 'ASC')
			->getQuery()
			->getResult();
	}

	public function searchServices($keywords)
	{
		$qb = $this->createQueryBuilder('j');
		if ($keywords) {
			$qb->andWhere('j.title LIKE :key OR j.description LIKE :key')
				->setParameter('key', '%' . $keywords . '%');
			$qb->andWhere('j.status = :status')
				->setParameter('status', constants::JOB_STATUS_ACTIVE);
			return $qb->orderBy('j.expiredDate', 'DESC')
				->getQuery()
				->getResult();
		}
	}

	public function searchByCategory($category)
	{
		$qb = $this->createQueryBuilder('j')
			->where('j.category =:name')
			->setParameter('name', $category)
			->andWhere('j.status = :status')
			->setParameter('status', constants::JOB_STATUS_ACTIVE)
			->orderBy('j.dateCreated', 'DESC');

		return $qb->getQuery()
			->getResult();
	}

	public function searchByLocation($location)
	{
		$qb = $this->createQueryBuilder('j');
		$qb->where('j.localtion =:location')
			->setParameter('location', $location)
			->orderBy('j.dateCreated', 'DESC');

		return $qb->getQuery()
			->getResult();
	}

	public function searchByCity($city)
	{
		$qb = $this->createQueryBuilder('j');
		$qb->where('j.city =:city')
			->setParameter('city', $city)
			->orderBy('j.dateCreated', 'DESC');

		return $qb->getQuery()
			->getResult();
	}

	public function searchByCompany($company)
	{
		$qb = $this->createQueryBuilder('j');
		$qb->where('j.company =:company')
			->setParameter('company', $company)
			->orderBy('j.dateCreated', 'DESC');

		return $qb->getQuery()
			->getResult();
	}

	public function search($keywords, $location)
	{
		$qb = $this->createQueryBuilder('j');
		if ($keywords) {
			$qb->andWhere('j.title LIKE :key OR j.description LIKE :key')
				->setParameter('key', '%' . $keywords . '%');
		}
		if ($location) {
			$qb->orWhere('j.your_localtion LIKE :key OR j.localtion LIKE :key')
				->setParameter('key', '%' . $keywords . '%');
		}
		$qb->andWhere('j.status = :status')
			->setParameter('status', constants::JOB_STATUS_ACTIVE);

		return $qb
			->orderBy('j.expiredDate', 'DESC')
			->getQuery()
			->getResult();
	}

	public function searchCatAndLocation($category, $location, $gender, $experience, $time)
	{
		$qb = $this->createQueryBuilder('j');
		if ($category != null) {
			$qb->andWhere('j.category =: key ')
				->setParameter('key', '%' . $category . '%');
		}
		if ($location != null) {
			$qb->orWhere('j.your_localtion LIKE :key OR j.localtion LIKE :key')
				->setParameter('key', '%' . $location . '%');
		}
		if ($gender != null) {
			$qb->orWhere('j.gender LIKE :key')
				->setParameter('key', '%' . $gender . '%');
		}
		if ($experience != null) {
			$qb->orWhere('j.experience LIKE :key')
				->setParameter('key', '%' . $experience . '%');
		}
		//			if ($time > 0) {
		//				$newTime = date("Ymd H: m: s", strtotime('- '.$time.' hours', time()));
		//				$qb->orWhere('j.dateCreated < :key')
		//					->setParameter('key', $newTime);
		//			}
		$qb->andWhere('j.status = :status')
			->setParameter('status', constants::JOB_STATUS_ACTIVE);

		return $qb
			->orderBy('j.dateCreated', 'DESC')
			->getQuery()
			->getResult();
	}

	public function expired()
	{
		return $this->createQueryBuilder('j')
			->update()
			->set('j.status', '?1')
			->setParameter(1, constants::JOB_STATUS_EXPIRED)
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
			->getResult();
	}

	public function requests($user)
	{
		$jobs = $this->createQueryBuilder('j')
			->andWhere('j.user = :val')
			->setParameter('val', $user)
			->orderBy('j.status', 'ASC')
			->getQuery()
			->getResult();
		$count = 0;
		for ($i = 0; $i < count($jobs); $i++) {
			$count += count($jobs[$i]->getUsers());
		}

		return $count;
	}

	public function finJobByUser($user)
	{
		return $this->createQueryBuilder('j')
			->andWhere('j.user = :val')
			->setParameter('val', $user)
			->orderBy('j.status', 'ASC')
			->orderBy('j.dateCreated', 'DESC')
			->getQuery()
			->getResult();
	}

	public function finServicesByUser($user)
	{
		return $this->createQueryBuilder('j')
			->andWhere('j.user = :val')
			->setParameter('val', $user)
			->orderBy('j.status', 'ASC')
			->getQuery()
			->getResult();
	}

	public function findOneBySomeField($value): ?Job
	{
		return $this->createQueryBuilder('j')
			->andWhere('j.exampleField = :val')
			->setParameter('val', $value)
			->getQuery()
			->getOneOrNullResult();
	}

	public function jobsByStatus($status)
	{
		return $this->createQueryBuilder('j')
			->where('j.status = :status')
			->setParameter('status', $status)
			->getQuery()
			->getResult();
	}

	/**
	 * @param $month
	 * @return mixed
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function countByMonth($month)
	{
		return $this->createQueryBuilder('j')
			->select('COUNT(j.id)')
			->where('MONTH(j.date) =:m')
			->setParameter('m', $month)
			->getQuery()
			->getSingleScalarResult();
	}
}