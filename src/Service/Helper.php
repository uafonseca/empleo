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
	use App\Entity\Notification;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//	use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
	use Vich\UploaderBundle\Mapping\PropertyMapping;
	
	class Helper extends Controller
	{
		/**
		 * Helper constructor.
		 */
		function __construct()
		{
		}
		
		public function getDirectoryNamerCompany(PropertyMapping $mapping)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			
			return '_files_'.$user->getId().'/';
		}
		
		public function expired()
		{
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			if ($currentUser->getPackage() == null) {
				return null;
			}
			if (null != $date_purchase = $currentUser->getDateOfPurchase()) {
				$pack = $currentUser->getPackage();
				if ($date_purchase->add(
						\DateInterval::createfromdatestring('+'.$pack->getVisibleDays().' day')
					) < new \DateTime('now')) {
					return null;
				}
			}
			if ($currentUser->getDateOfPurchase() != null) {
				return array(
					'days' => $currentUser->getDateOfPurchase()->diff(new \DateTime())->format('%a'),
					'public' => $currentUser->getNumPosts(),
				);
			} else {
				return array(
					'days' => 0,
					'public' => $currentUser->getNumPosts(),
				);
			}
			
		}
		
		public function loadNotifications()
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			if (null != $user) {
				$em = $this->getDoctrine()->getManager();
				$notifications = $em->getRepository(Notification::class)->findBy(
					array(
						'user' => $user,
						'active' => true,
					),
					array(
						'date' => 'DESC',
					)
				);
				
				return $notifications;
			}
			
			return null;
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