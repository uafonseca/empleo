<?php
	/**
	 * Created by PhpStorm.
	 * User: Ubel
	 * Date: 8/2/2019
	 * Time: 18:26
	 */
	
	namespace App\Controller;
	
	use App\Entity\Job;
	use App\Entity\Metadata;
	use App\Entity\Notification;
	use App\Entity\Payment;
	use App\Entity\Policy;
	use App\Entity\Resume;
	use App\Entity\User;
	use App\Form\ResumeFilesType;
	use App\Form\ResumeType;
	use App\Form\UserFullyEmployerType;
	use App\Form\UserFullyType;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\BinaryFileResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\ResponseHeaderBag;
	use Symfony\Component\Routing\Annotation\Route;
	use App\constants;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
	use Symfony\Component\Filesystem\Filesystem;
	use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
	
	class mainController extends Controller
	{
		
		/**
		 * @Route("/mail",name="mail")
		 */
		public function mailView()
		{
			return $this->render('mail/code.html.twig', array('code' => 1234));
		}
		
		public function verificateUser(AuthorizationCheckerInterface $authChecker)
		{
			if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
				$fileSystem = new Filesystem();
				try {
					$base = $this->getParameter('kernel.project_dir').'/public';
					$path = $this->getParameter('app.path.user_images');
					$user = $this->get('security.token_storage')->getToken()->getUser();
					$dir = $base.$path.'/_files_'.$user->getUsername();
					if (!$fileSystem->exists($dir)) {
						$fileSystem->mkdir($dir);
						if ($fileSystem->exists($base.$path.'/'.$user->getImage())) {
							$fileSystem->copy($base.$path.'/'.$user->getImage(), $dir.'/'.$user->getImage());
							$fileSystem->remove($base.$path.'/'.$user->getImage());
						}
					}
				} catch (IOExceptionInterface $exception) {
					echo $exception->getMessage();
//					die;
				}
				if (!$this->container->get('app.service.checker')->isUserValid()) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * @Route("/pricing",name="pricing_page")
		 */
		public function pricing(){
			$em = $this->getDoctrine()->getManager();
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$is_admin = in_array('ROLE_ADMIN', $currentUser->getRoles());
			$packages = $em->getRepository(Payment::class)->findBy(array('adminPayment' => $is_admin));
			return $this->render('site/pricing.html.twig',[
				'notifications' => $this->loadNotifications(),
				'packages' => $packages,
			]);
		}
		/**
		 * @Route("/checkout/{packId}",name="checkout")
		 */
		public function checkout($packId){
			$em = $this->getDoctrine()->getManager();
			$package = $em->getRepository(Payment::class)->find($packId);
			return $this->render('site/checkout.html.twig',[
				'notifications' => $this->loadNotifications(),
				'package' => $package,
			]);
		}
		
		public function updateJobsFiles()
		{
			$em = $this->getDoctrine()->getManager();
			$jobs = $em->getRepository(Job::class)->findAll();
			$base = $this->getParameter('kernel.project_dir').'/public';
			$path = $this->getParameter('app.path.company_images');
			$fileSystem = new Filesystem();
			foreach ($jobs as $job) {
				$dir = $base.$path.'/_user_'.$job->getUser()->getId();
				try {
					$fileSystem->mkdir($dir);
					if ($fileSystem->exists($base.$path.'/'.$job->getImage())) {
						$fileSystem->copy($base.$path.'/'.$job->getImage(), $dir.'/'.$job->getImage());
						$fileSystem->remove($base.$path.'/'.$job->getImage());
					}
				} catch (IOExceptionInterface $exception) {
					echo $exception->getMessage();
					die;
				}
			}
		}
		
		/**
		 * @Route("/",name="homepage")
		 */
		public function index(
			AuthorizationCheckerInterface $authChecker
		): Response {
			$this->updateJobsFiles();
			$verificated = $this->verificateUser($authChecker);
			$em = $this->getDoctrine()->getManager();
			$this->container->get('app.service.checker')->checkJobs();
			
			return $this->render(
				'site/job/index.html.twig',
				[
					'verificated_acount' => $verificated,
					'notifications' => $this->loadNotifications(),
					'jobs' => $em->getRepository(Job::class)->jobsByStatus(constants::JOB_STATUS_ACTIVE),
					'locations' => $this->container->get('app.service.helper')->loadLocations(),
					'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
					'citys' => $this->container->get('app.service.helper')->loadCityes(),
					'company' => $this->container->get('app.service.helper')->LoadCompany(),
					'entity' => count($em->getRepository(User::class)->findByRole('ROLE_ADMIN')),
				]
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
					),
					10
				);
				
				return $notifications;
			}
			
			return null;
		}
		
		/**
		 * @Route("/about", name="about")
		 */
		public function about()
		{
			return new Response(
				'<html><body>about </body></html>'
			);
		}
		
		/**
		 * @Route("/terms", name="site_policy")
		 */
		public function policy(AuthorizationCheckerInterface $authChecker)
		{
			
			$em = $this->getDoctrine()->getManager();
			$verificated = $this->verificateUser($authChecker);
			
			return $this->render(
				'site/policy.html.twig',
				array(
					'verificated_acount' => $verificated,
					'notifications' => $this->loadNotifications(),
					'terms' => $em->getRepository(Policy::class)->load(),
				)
			);
		}
		
		
		/**
		 * @Route("/contact", name="contact")
		 */
		public function contact(AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			
			return new Response(
				'<html><body>Contact </body></html>'
			);
		}
		
		/**
		 * @Route("/dashboard", name="dashboard")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function dashboard(AuthorizationCheckerInterface $authChecker)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$verificated = $this->verificateUser($authChecker);
			if (in_array('ROLE_ADMIN', $user->getRoles())) {
				$em = $this->getDoctrine()->getManager();
				$public = $em->getRepository(Job::class)->countJob($user);
				
				return $this->render(
					'user/employer/dashboard.html.twig',
					array(
						'notifications' => $this->loadNotifications(),
						'public' => $public,
						'requests' => $em->getRepository(Job::class)->requests($user),
						'expired'=>$this->container->get('app.service.helper')->expired()
					)
				);
			} else {
				return $this->render(
					'user/dashboard.html.twig',
					array(
						'verificated_acount' => $verificated,
						'notifications' => $this->loadNotifications(),
						'expired'=>$this->container->get('app.service.helper')->expired()
					)
				);
			}
		}
		
		
		/**
		 * @Route("/dashboard/edit", name="dashboard_edit")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function edit_profile(Request $request, AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$is_admin = in_array('ROLE_ADMIN', $user->getRoles());
			if ($is_admin) {
				$form = $this->createForm(UserFullyEmployerType::class, $user);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$entityManager = $this->getDoctrine()->getManager();
					$entityManager->flush();
					$notification = new Notification();
					$notification->setDate(new \DateTime());
					$notification->setType(constants::NOTIFICATION_PROFILE_UPDATE);
					$notification->setContext("Su perfil se ha actualizado correctamente");
					$notification->setUser($user);
					$notification->setActive(true);
					$entityManager->persist($notification);
					$entityManager->flush();
					
					return $this->redirectToRoute('dashboard');
				}
			} else {
				$form = $this->createForm(UserFullyType::class, $user);
				$form->handleRequest($request);
				if ($form->isSubmitted() && $form->isValid()) {
					$entityManager = $this->getDoctrine()->getManager();
					$entityManager->flush();
					$notification = new Notification();
					$notification->setDate(new \DateTime());
					$notification->setType(constants::NOTIFICATION_PROFILE_UPDATE);
					$notification->setContext("Su perfil se ha actualizado correctamente");
					$notification->setUser($user);
					$notification->setActive(true);
					$entityManager->persist($notification);
					$entityManager->flush();
					
					return $this->redirectToRoute('dashboard_resume_edit');
				}
			}
			if ($is_admin) {
				return $this->render(
					'user/employer/edit_profile.html.twig',
					array(
						'verificated_acount' => $verificated,
						'form' => $form->createView(),
						'notifications' => $this->loadNotifications(),
						'expired'=>$this->container->get('app.service.helper')->expired()
					)
				);
			} else {
				return $this->render(
					'user/edit_profile.html.twig',
					array(
						'form' => $form->createView(),
						'notifications' => $this->loadNotifications(),
						'expired'=>$this->container->get('app.service.helper')->expired()
					)
				);
			}
		}
		
		/**
		 * @Route("/ajax", name="ajax")
		 */
		public function deleteNotification($id)
		{
			//echo json_encode($id);
		}
		
		/**
		 * @Route("/dashboard/resume", name="dashboard_resume")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function resume(AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$em = $this->getDoctrine()->getManager();
			$metas = $em->getRepository(Metadata::class)->findBy(array("resume" => $user->getResume()));
			$cv = $user->getResume()->getCv();
			$cart = $user->getResume()->getCart();
			
			return $this->render(
				'user/resume.html.twig',
				array(
					'verificated_acount' => $verificated,
					'resume' => $user->getResume(),
					'notifications' => $this->loadNotifications(),
					'cv' => $cv,
					'cart' => $cart,
					'metas' => $metas,
				)
			);
		}
		
		/**
		 * @Route("/dashboard/resume/edit", name="dashboard_resume_edit")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function resumeEdit(Request $request, AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$entityManager = $this->getDoctrine()->getManager();
			if ($user->getResume() == null) {
				$resume = new Resume();
				$resume->setUser($user);
				$em = $this->getDoctrine()->getManager();
				$em->persist($resume);
				$em->flush();
			}
			$resume = $entityManager->getRepository(Resume::class)->findOneBy(array('user' => $user,));
			$metas = $entityManager->getRepository(Metadata::class)->findBy(array("resume" => $resume));
			$form = $this->createForm(ResumeType::class, $resume);
			$formFiles = $this->createForm(ResumeFilesType::class, $resume);
			$formFiles->handleRequest($request);
			if ($formFiles->isSubmitted() && $formFiles->isValid()) {
				$resume->setUpdatedAt(new \DateTime("now"));
				$entityManager->flush();
				$notification = new Notification();
				$notification->setDate(new \DateTime());
				$notification->setType(constants::METADATA_RESUME_EDIT);
				$notification->setContext("Su curriculum se ha actualizado correctamente");
				$notification->setUser($user);
				$notification->setActive(true);
				$entityManager->persist($notification);
				$entityManager->flush();
			}
			
			return $this->render(
				'user/resume_edit.html.twig',
				array(
					'verificated_acount' => $verificated,
					'resume' => $resume,
					'notifications' => $this->loadNotifications(),
					'form_resume' => $form->createView(),
					'form_files' => $formFiles->createView(),
					'metas' => $metas,
				)
			);
		}
		
		/**
		 * @Route("/dashboard/bookmarked", name="dashboard_bookmarked")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function bookMarked(AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$marked = $user->getBookmarked();
			$jobs = array();
			$entityManager = $this->getDoctrine()->getManager();
			foreach ($marked as $id) {
				$jobs[] = $entityManager->getRepository(Job::class)->find($id);
			}
			
			return $this->render(
				'user/mark.html.twig',
				array(
					'verificated_acount' => $verificated,
					'jobs' => $jobs,
					'notifications' => $this->loadNotifications(),
				
				)
			);
		}
		
		/**
		 * @Route("/dashboard/applied", name="dashboard_applied")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function applied(AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$marked = $user->getApplied();
			$jobs = array();
			$entityManager = $this->getDoctrine()->getManager();
			foreach ($marked as $id) {
				$jobs[] = $entityManager->getRepository(Job::class)->find($id);
			}
			
			return $this->render(
				'user/applied.html.twig',
				array(
					'verificated_acount' => $verificated,
					'jobs' => $jobs,
					'notifications' => $this->loadNotifications(),
				
				)
			);
		}
		
		/**
		 * @Route("/candidates", name="candidates")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function candidates(AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$entityManager = $this->getDoctrine()->getManager();
			$users = $entityManager->getRepository(User::class)->findAll();
			$i = 0;
			foreach ($users as $user) {
				if (!$user->getCandidate()) {
					unset($users[$i]);
				}
				$i++;
			}
			$path = $this->getParameter('app.path.user_cv').'/';
			
			return $this->render(
				'site/candidate.html.twig',
				array(
					'verificated_acount' => $verificated,
					'candidates' => $users,
					'notifications' => $this->loadNotifications(),
					'url' => $path,
				)
			);
		}
		
		/**
		 * @Route("/manage/candidates", name="manage_candidates")
		 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
		 * @IsGranted("IS_AUTHENTICATED_FULLY")
		 */
		public function manageCandidates(Request $request, AuthorizationCheckerInterface $authChecker)
		{
			$verificated = $this->verificateUser($authChecker);
			$entityManager = $this->getDoctrine()->getManager();
			$users = $entityManager->getRepository(User::class)->findCandidatesList(
				$user = $this->get('security.token_storage')->getToken()->getUser()->getId()
			);
			$i = 0;
			foreach ($users as $user) {
				if (!$user->getCandidate()) {
					unset($users[$i]);
				}
				$i++;
			}
			$pagination = $this->get('knp_paginator')->paginate(
				$users,
				$request->query->getInt('page', 1),
				10
			);
			$pagination->setTemplate('site/pagination.html.twig');
			$path = $this->getParameter('app.path.user_cv').'/';
			
			return $this->render(
				'user/employer/candidate.html.twig',
				array(
					'verificated_acount' => $verificated,
					'candidates' => $pagination,
					'notifications' => $this->loadNotifications(),
					'url' => $path,
					'expired'=>$this->container->get('app.service.helper')->expired()
				)
			);
		}
		
		/**
		 * @Route("/candidate/{id}/detail", name="canditate_detail")
		 */
		public function candidateDetails($id, AuthorizationCheckerInterface $authChecker)
		{
			$em = $this->getDoctrine()->getManager();
			$canditate = $em->getRepository(User::class)->find($id);
			$verificated = $this->verificateUser($authChecker);
			
			return $this->render(
				'user/employer/candidate_detail.html.twig',
				array(
					'verificated_acount' => $verificated,
					'notifications' => $this->loadNotifications(),
					'candidate' => $canditate,
				)
			);
		}
		
		/**
		 * @Route("/dowload/cv/{name}", name="dowload_cv")
		 *
		 */
		public function dowloadCv($name)
		{
			$base = $this->getParameter('kernel.project_dir').'/public/';
			$path = $this->getParameter('app.path.user_cv').'/';
			$response = new BinaryFileResponse($base.$path.$name);
			$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
			
			return $response;
		}
		
		/**
		 * @Route("acount/verificate", name="acount_verificate")
		 *
		 */
		public function verificateAcount(Request $request)
		{
			if (null !== $request->get('code')) {
				$currentUser = $this->get('security.token_storage')->getToken()->getUser();
				if ($currentUser->getSecret() == $request->get('code')) {
					$em = $this->getDoctrine()->getManager();
					$currentUser->setVerificated(true);
					$em->flush();
					
					return $this->redirectToRoute('homepage');
				} else {
					$this->addFlash('error', 'Código de verificación incorrecto');
					
					return $this->redirectToRoute('homepage');
				}
			}
			
			return $this->render('site/acount_verificate.html.twig');
		}
		
		/**
		 * @Route("send/code", name="send_code")
		 */
		public function sendCode(\Swift_Mailer $mailer)
		{
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			if (empty($currentUser->getSecret())) {
				$currentUser->setSecret(rand(10000, 99999));
			}
			$message = (new \Swift_Message('Código de verificación'))
				->setFrom('emplearecuador@gmail.com')
				->setBody(
					$this->renderView(
						'mail/code.html.twig',
						[
							'code' => $currentUser->getSecret(),
						]
					),
					'text/html'
				)
				->setTo($currentUser->getEmail());
			$mailer->send($message);
			$this->addFlash('success', 'Se ha enviado el código a '.$currentUser->getEmail());
			
			return $this->redirectToRoute('homepage');
		}
		/**
		 * @Route("admin/package", name="admin_package")
		 */
		public function packageIndex()
		{
			$em = $this->getDoctrine()->getManager();
			$packages = $em->getRepository(Payment::class)->findAll();
			return $this->render('admin/dashboard.html.twig',
				array(
					'notifications'=>$this->container->get('app.service.helper')->loadNotifications(),
					'packages'=>$packages,
				));
		}
	}
