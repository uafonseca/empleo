<?php
	
	namespace App\Controller;
	
	use App\constants;
	use App\Entity\Anouncement;
	use App\Entity\Job;
	use App\Entity\Notification;
	use App\Entity\Payment;
	use App\Form\ServiceJobType;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	
	class ServiceController extends Controller
	{
		/**
		 * @Route("/service", name="service")
		 */
		public function index()
		{
			return $this->render(
				'service/index.html.twig',
				[
					'controller_name' => 'ServiceController',
				]
			);
		}
		
		
		/**
		 * @Route("/service/new", name="service_new")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function serviceNew(Request $request)
		{
			$post = new Anouncement();
			$form = $this->createForm(ServiceJobType::class, $post);
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$entityManager = $this->getDoctrine()->getManager();
			$packages = $entityManager->getRepository(Payment::class)->findBy(array('adminPayment' => false));
			$post->setDate(new \DateTime("now"));
			$form->handleRequest($request);
			if ($form->isSubmitted() && $form->isValid()) {
				$post->setExpiredDate(
					$post->getDate()->add(\DateInterval::createfromdatestring('+'.$currentUser->getPackage()->getVisibleDays().' day'))
				);
				$post->setDate(new \DateTime("now"));
				$post->setStatus(constants::JOB_STATUS_ACTIVE);
				$entityManager->flush();
				$currentUser->setNumPosts($currentUser->getNumPosts() - 1);
				$post->setUser($currentUser);
				$entityManager->persist($post);
				$entityManager->flush();
				$notification = new Notification();
				$notification->setType(constants::NOTIFICATION_JOB_CREATE);
				$notification->setContext("Servicio creado satisfactoriamente");
				$notification->setUser($this->get('security.token_storage')->getToken()->getUser());
				$notification->setActive(true);
				$entityManager->persist($notification);
				return $this->redirectToRoute('service_manage');
			}
			$expired = $this->container->get('app.service.helper')->expired();
			if (null == $expired) {
				return $this->redirectToRoute('pricing_page');
			} elseif ( $expired['days'] == 0 || $expired['public'] == 0 ) {
				return $this->redirectToRoute('pricing_page');
			}
			return $this->render(
				'service/new.html.twig',
				[
					'form' => $form->createView(),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'packages' => $packages,
					'expired'=>$expired,
				]
			);
		}
		
		/**
		 * @Route("/service/manage", name="service_manage")
		 */
		public function manage(Request $request)
		{
			$em = $this->getDoctrine()->getManager();
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$jobs = $em->getRepository(Anouncement::class)->findBy(array('User' => $user),array('date'=>'desc'));
			$pagination = $this->get('knp_paginator')->paginate(
				$jobs,
				$request->query->getInt('page', 1),
				5
			);
			$pagination->setTemplate('site/pagination.html.twig');
			
			return $this->render(
				'service/manage.html.twig',
				[
					'jobs' => $pagination,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'expired'=>$this->container->get('app.service.helper')->expired()
				]
			);
		}
		
		/**
		 * @Route("/service/view/{id}", name="service_view")
		 */
		public function serviceView($id){
			$em= $this->getDoctrine()->getManager();
			$service = $em->getRepository(Anouncement::class)->find($id);
			return $this->render('service/view.html.twig',array(
				'job'=>$service,
				'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				'expired'=>$this->container->get('app.service.helper')->expired(),
			));
		}
		
		/**
		 * @Route("/service/list", name="service_list")
		 */
		public function serviceList(Request $request)
		{
			$em = $this->getDoctrine()->getManager();
			$jobs = $em->getRepository(Anouncement::class)->findBy(array('status'=>constants::JOB_STATUS_ACTIVE));
			$pagination = $this->get('knp_paginator')->paginate(
				$jobs,
				$request->query->getInt('page', 1),
				5
			);
			$pagination->setTemplate('site/pagination.html.twig');
			
			return $this->render(
				'service/list.html.twig',
				[
					'jobs' => $pagination,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'professions' => $this->container->get('app.service.helper')->loadProfessions(),
				]
			);
		}
		
		/**
		 * @Route("/services/search",name="services_search")
		 */
		public function search(Request $request)
		{
			$keywords = $request->request->get('keywords');
			$em = $this->getDoctrine()->getManager();
			$pagination = $this->get('knp_paginator');
			if (empty($keywords)) {
				return $this->redirectToRoute('service_list');
			} else {
				$pagination->paginate(
					$em->getRepository(Job::class)->searchServices($keywords),
					$request->query->getInt('page', 1),
					10
				);
			}
			
			return $this->render(
				'service/list.html.twig',
				array(
					'jobs' => $pagination->paginate(
						$em->getRepository(Job::class)->searchServices($keywords),
						$request->query->getInt('page', 1),
						10
					),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'search' => 1,
				)
			);
		}
	}
