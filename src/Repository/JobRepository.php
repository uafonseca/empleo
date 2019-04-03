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
				->andWhere('j.is_service = false')
				->orWhere('j.is_service is NULL')
				->setMaxResults(10)
				->getQuery()
				->getResult();
		}
		public function getCountCategory($key){
			return $this->createQueryBuilder('c')
				->select('count(c) as count')
				->innerJoin('c.category','category')
				->where('category.name LIKE :key')
				->setParameter('key','%'.$key.'%')
				->getQuery()
				->getOneOrNullResult();
		}
		public function countJob($user)
		{
			return count(
				$this->createQueryBuilder('j')
					->andWhere('j.user = :user')
					->setParameter('user', $user)
					->getQuery()
					->getResult()
			);
		}
		
		public function getLocationsByName()
		{
			return $this->createQueryBuilder('j')
				->select('j.localtion as name')
				->where('j.is_service = false')
				->orWhere('j.is_service is null')
				->distinct()
				->getQuery()
				->getResult();
		}
		
		public function getCityesByName()
		{
			return $this->createQueryBuilder('j')
				->select('j.city as name')
				->where('j.is_service = false')
				->orWhere('j.is_service is null')
				->distinct()
				->getQuery()
				->getResult();
		}
		
		public function getCompanyByName()
		{
			return $this->createQueryBuilder('j')
				->select('j.company_name as name')
				->where('j.is_service = false')
				->orWhere('j.is_service is null')
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
		public function listServices(){
			return $this->createQueryBuilder('j')
				->where('j.is_service = true')
				->andWhere('j.status =:status')
				->setParameter('status',constants::JOB_STATUS_ACTIVE)
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
		
		public function search($keywords, $location)
		{
			$qb = $this->createQueryBuilder('j');
			if ($keywords) {
				$qb->andWhere('j.title LIKE :key OR j.description LIKE :key')
					->setParameter('key', '%'.$keywords.'%');
			}
			if ($location) {
				$qb->orWhere('j.your_localtion LIKE :key OR j.localtion LIKE :key')
					->setParameter('key', '%'.$keywords.'%');
			}
			$qb->andWhere('j.status = :status')
				->setParameter('status',constants::JOB_STATUS_ACTIVE);
			
			return $qb
				->andWhere('j.is_service = false')
				->orWhere('j.is_service is NULL')
				->orderBy('j.expiredDate', 'DESC')
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
				->getQuery()
				->getResult();
		}
		public function finServicesByUser($user)
		{
			return $this->createQueryBuilder('j')
				->andWhere('j.user = :val')
				->setParameter('val', $user)
				->andWhere('j.is_service = true')
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
				->andWhere('j.is_service = false')
				->orWhere('j.is_service is NULL')
				->getQuery()
				->getResult();
		}
		
//		public function updateExpired()
//		{
//			$this->createQueryBuilder('j')
//				->update('App\Entity\Job', 'j')
//				->set('j.status', '?1')
//				->where('j.expiredDate > ?2')
//				->setParameter(1, constants::JOB_STATUS_EXPIRED)
//				->setParameter(2, new \DateTime("now"))
//				->getQuery()
//				->getResult();
//		}
	}
