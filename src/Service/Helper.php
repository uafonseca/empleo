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
	use App\Entity\Profession;
	use phpDocumentor\Reflection\DocBlock\Tags\Return_;
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
			$days = 0;
			$daysService = 0;
			if ($currentUser->getDateOfPurchase() != null && $currentUser->getPackage()!=null) {
				$pack = $currentUser->getPackage();
				$date_purchase = $currentUser->getDateOfPurchase();
				$days = $currentUser->getDateOfPurchase()->add(\DateInterval::createfromdatestring('+'.$pack->getVisibleDays().' day'))->diff(new \DateTime())->format('%a');
				if($date_purchase->add(\DateInterval::createfromdatestring('+'.$pack->getVisibleDays().' day'))< new \DateTime('now')){
					$days = -1;
				}
			}
			if ($currentUser->getDateOfPurchaseService() != null &&  $currentUser->getPackageServices() !=null) {
				$pack = $currentUser->getPackageServices();
				$date_purchase = $currentUser->getDateOfPurchaseService();
				$daysService = $currentUser->getDateOfPurchaseService()->add(\DateInterval::createfromdatestring('+'.$pack->getVisibleDays().' day'))->diff(new \DateTime())->format('%a');
				if($date_purchase->add(\DateInterval::createfromdatestring('+'.$pack->getVisibleDays().' day'))< new \DateTime('now')){
					$daysService = -1;
				}
			}
			return array(
				'days' => $days,
				'public' => $currentUser->getNumPosts(),
				'daysService' => $daysService,
				'publicService' => $currentUser->getNumPostsServices(),
			);
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
					$ouput[] = array('name' => $name, 'count' => $count['count'],'id' => $value['id']);
				}
			}
			
			return $ouput;
		}
		
		public function loadProfessions()
		{
			$em = $this->getDoctrine()->getManager();
			
			return $em->getRepository(Profession::class)->findAll();
		}
		
		public function loadLocations()
		{
			$em = $this->getDoctrine()->getManager();
			$all = $em->getRepository(Job::class)->getLocationsByName();
			$ouput = array();
			foreach ($all as $value) {
				$name = $value['name'];
				if (!empty($count = $em->getRepository(Job::class)->getCount($name))) {
					if ($name != "") {
						$ouput[] = array('name' => $name, 'count' => $count['count']);
					}
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
					if ($name != "") {
						$ouput[] = array('name' => $name, 'count' => $count['count']);
					}
				}
			}
			
			return $ouput;
		}
	}