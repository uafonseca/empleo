<?php
	/**
	 * Created by PhpStorm.
	 * User: Ubel
	 * Date: 23/3/2019
	 * Time: 20:28
	 */
	
	namespace App\Service;
	
	use App\constants;
	use App\Entity\Job;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	
	class Checker extends Controller
	{
		
		/**
		 * Checker constructor.
		 */
		public function __construct()
		{
		}
		
		public function checkJobs()
		{
			$em = $this->getDoctrine()->getManager();
			$jobs = $em->getRepository(Job::class)->findAll();
			foreach ($jobs as $job) {
				if ($job->getStatus() == constants::JOB_STATUS_ACTIVE) {
					if($job->getExpiredDate() < new \DateTime('now'))
					{
						$job->setStatus(constants::JOB_STATUS_EXPIRED);
					}
				}
				if($job->getStatus() == constants::JOB_STATUS_PENDING)
				{
					if($job->getDate() >= new \DateTime('now'))
					{
						$job->setStatus(constants::JOB_STATUS_ACTIVE);
					}
				}
			}
			$em->flush();
		}
		
	}