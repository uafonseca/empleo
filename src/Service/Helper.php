<?php
	/**
	 * Created by PhpStorm.
	 * User: FonsecaGay
	 * Date: 23/3/2019
	 * Time: 13:26
	 */
	
	namespace App\Service;
	
	use App\Entity\Category;
	use App\Entity\Job;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	
	class Helper extends Controller
	{
		/**
		 * Helper constructor.
		 */
		function __construct()
		{
		}
		
		public function loadCategorys()
		{
			$em = $this->getDoctrine()->getManager();
			$all = $em->getRepository(Category::class)->getCategoryesName();
			$ouput = array();
			foreach ($all as $value) {
				$name = $value['name'];
				if (!empty($count = $em->getRepository(Job::class)->getCountCategory($name))) {
					$ouput[] = array('name' => $name, 'count' => $count['count']);
				}
			}
			
			return $ouput;
		}
		
		public function loadLocations()
		{
			$em = $this->getDoctrine()->getManager();
			$all = $em->getRepository(Job::class)->getLocationsByName();
			$ouput = array();
			foreach ($all as $value) {
				$name = $value['name'];
				if (!empty($count = $em->getRepository(Job::class)->getCount($name))) {
					$ouput[] = array('name' => $name, 'count' => $count['count']);
				}
			}
			
			return $ouput;
		}
		
		public function loadCityes()
		{
			$em = $this->getDoctrine()->getManager();
			$all = $em->getRepository(Job::class)->getCityesByName();
			$ouput = array();
			foreach ($all as $value) {
				$name = $value['name'];
				if (!empty($count = $em->getRepository(Job::class)->getCityCount($name))) {
					$ouput[] = array('name' => $name, 'count' => $count['count']);
				}
			}
			
			return $ouput;
		}
		
		public function LoadCompany()
		{
			$em = $this->getDoctrine()->getManager();
			$all = $em->getRepository(Job::class)->getCompanyByName();
			$ouput = array();
			foreach ($all as $value) {
				$name = $value['name'];
				if (!empty($count = $em->getRepository(Job::class)->getCompanyCount($name))) {
					$ouput[] = array('name' => $name, 'count' => $count['count']);
				}
			}
			
			return $ouput;
		}
	}