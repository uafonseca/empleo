<?php
	/**
	 * Created by PhpStorm.
	 * User: Ubel
	 * Date: 2/18/2019
	 * Time: 2:38 PM
	 */
	
	namespace App\Controller;
	
	use App\constants;
	use App\Entity\Category;
	use App\Entity\Job;
	use App\Entity\Notification;
	use App\Entity\Payment;
	use App\Entity\PaymentForJobs;
	use App\Entity\PaymentForServices;
	use App\Entity\User;
	use Symfony\Component\Form\FormError;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use App\Form\JobType;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	
	class JobController extends Controller
	{
		/**
		 * @Route("/job/new/", name="job_new")
		 * @IsGranted("ROLE_ADMIN")
		 */
		public function jobNew(Request $request)
		{
			$post = new Job();
			$form = $this->createForm(JobType::class, $post);
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$entityManager = $this->getDoctrine()->getManager();
			$user = $entityManager->getRepository(User::class)->find($currentUser->getId());
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				$payment = $user->getPackage();
				$post->setExpiredDate(
					$post->getDate()->add(\DateInterval::createfromdatestring('+'.$payment->getVisibleDays().' day'))
				);
				if ($form->get('date')->getData() < date("y-m-d", strtotime("today"))) {
					$form->get('date')->addError(new FormError("La fecha de debe ser mayor que la fecha acual"));
					
					return $this->render(
						'site/job/job.html.twig',
						[
							'form' => $form->createView(),
							'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
							'expired' => $this->container->get('app.service.helper')->expired(),
						]
					);
				} elseif ($form->get('date')->getData() > new \DateTime("now")) {
					$post->setStatus(constants::JOB_STATUS_PENDING);
				} else {
					$post->setStatus(constants::JOB_STATUS_ACTIVE);
				}
				$user->setNumPosts($user->getNumPosts() - 1);
				$entityManager->flush();
				$post->setUser($user);
				$post->setDateCreated(new \DateTime("now"));
				$entityManager->persist($post);
				$entityManager->flush();
				$notification = new Notification();
				$notification->setDate(new \DateTime());
				$notification->setType(constants::NOTIFICATION_JOB_CREATE);
				$notification->setContext("Empleo creado satisfactoriamente");
				$notification->setUser($this->get('security.token_storage')->getToken()->getUser());
				$notification->setActive(true);
				$entityManager->persist($notification);
				return $this->redirectToRoute('job_manage');
			}
			if (null == $this->container->get('app.service.helper')->expired()) {
				return $this->redirectToRoute('pricing_page');
			} elseif (
				$this->container->get('app.service.helper')->expired()['days'] == -1
				|| $this->container->get('app.service.helper')->expired()['public'] == 0
			) {
				return $this->redirectToRoute('pricing_page');
			}
			return $this->render(
				'site/job/job.html.twig',
				[
					'form' => $form->createView(),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'expired' => $this->container->get('app.service.helper')->expired(),
				]
			);
		}

        /**
         * @Route("/search/category/{cat}",name="search_category")
         * @param Request $request
         * @param $cat
         * @return mixed
         */
		public function searchByCategory(Request $request, $cat)
		{
			$em = $this->getDoctrine()->getManager();
			$category = $em->getRepository(Category::class)->find($cat);
			$pagination = $this->get('knp_paginator');
			
			return $this->render(
				'site/job/list.html.twig',
				array(
					'jobs' => $pagination->paginate(
						$em->getRepository(Job::class)->searchByCategory($category),
						$request->query->getInt('page', 1),
						10
					),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'search' => 1,
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
				)
			);
		}
		
		/**
		 * @Route("/search/location/{location}",name="search_location")
		 */
		public function searchByLocation(Request $request, $location)
		{
			$em = $this->getDoctrine()->getManager();
			$pagination = $this->get('knp_paginator');
			
			return $this->render(
				'site/job/list.html.twig',
				array(
					'jobs' => $pagination->paginate(
						$em->getRepository(Job::class)->searchByLocation($location),
						$request->query->getInt('page', 1),
						10
					),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'search' => 1,
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
				)
			);
		}
		
		/**
		 * @Route("/search/city/{city}",name="search_city")
		 */
		public function searchByCity(Request $request, $city)
		{
			$em = $this->getDoctrine()->getManager();
			$pagination = $this->get('knp_paginator');
			
			return $this->render(
				'site/job/list.html.twig',
				array(
					'jobs' => $pagination->paginate(
						$em->getRepository(Job::class)->searchByCity($city),
						$request->query->getInt('page', 1),
						10
					),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'search' => 1,
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
				)
			);
		}
		
		/**
		 * @Route("/search/company/{company}",name="search_company")
		 */
		public function searchByCompany(Request $request, $company)
		{
			$em = $this->getDoctrine()->getManager();
			$pagination = $this->get('knp_paginator');
			
			return $this->render(
				'site/job/list.html.twig',
				array(
					'jobs' => $pagination->paginate(
						$em->getRepository(Job::class)->searchByCompany($company),
						$request->query->getInt('page', 1),
						10
					),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'search' => 1,
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
				)
			);
		}
		
		/**
		 * @Route("/search",name="search")
		 */
		public function search(Request $request)
		{
			$keywords = $request->request->get('keywords');
			$location = $request->request->get('location');
			$em = $this->getDoctrine()->getManager();
			$pagination = $this->get('knp_paginator');
			if (empty($keywords) && empty($location)) {
				return $this->redirectToRoute('homepage');
			} else {
				return $this->render(
					'site/job/list.html.twig',
					array(
						'jobs' => $pagination->paginate(
							$em->getRepository(Job::class)->search($keywords, $location),
							$request->query->getInt('page', 1),
							10
						),
						'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
						'search' => 1,
						'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
						'locations' => $this->container->get('app.service.helper')->loadLocations(),
					)
				);
			}
		}
		
		/**
		 * @Route("/job/list/", name="job_list")
		 */
		public function jobList(Request $request)
		{
			$em = $this->getDoctrine()->getManager();
			$jobs = $em->getRepository(Job::class)->findBy(
				array('status' => constants::JOB_STATUS_ACTIVE),
				array('dateCreated' => 'desc')
			);
			$em->getRepository(Job::class)->expired();
			$pagination = $this->get('knp_paginator')->paginate(
				$jobs,
				$request->query->getInt('page', 1),
				10
			);
			$pagination->setTemplate('site/pagination.html.twig');
			
			return $this->render(
				'site/job/list.html.twig',
				[
					'jobs' => $pagination,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
				]
			);
		}
		
		/**
		 * @Route("/job/{id}/", name="job_show")
		 */
		public function jobShow($id)
		{
			$em = $this->getDoctrine()->getManager();
			$job = $em->getRepository(Job::class)->find($id);
			
			return $this->render(
				'site/job/view.html.twig',
				[
					'job' => $job,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				]
			);
		}
		
		/**
		 * @Route("/manage/job/", name="job_manage")
		 * @IsGranted("ROLE_ADMIN")
		 */
		public function manage(Request $request)
		{
			$em = $this->getDoctrine()->getManager();
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$jobs = $em->getRepository(Job::class)->finJobByUser($user);
			$pagination = $this->get('knp_paginator')->paginate(
				$jobs,
				$request->query->getInt('page', 1),
				5
			);
			$pagination->setTemplate('site/pagination.html.twig');
			
			return $this->render(
				'user/employer/manage_job.html.twig',
				[
					'jobs' => $pagination,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'expired' => $this->container->get('app.service.helper')->expired(),
				]
			);
		}
	}