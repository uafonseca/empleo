<?php
	/**
	 * Created by PhpStorm.
	 * User: Ubel
	 * Date: 23/02/2019
	 * Time: 17:34
	 */
	
	namespace App\Controller;
	
	
	use App\Entity\Anouncement;
	use App\Entity\Category;
	use App\Entity\Job;
	use App\Entity\Metadata;
	use App\Entity\Notification;
	use App\Entity\Profession;
	use App\Entity\Resume;
	use App\Entity\Service;
	use function Deployer\add;
	use http\Client\Response;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use App\constants;
	
	class AjaxController extends Controller
	{
		
		/**
		 * @Route("/ajax/job/remove", name="ajax_job_remove")
		 */
		function removeJob(Request $request)
		{
			$id = $request->request->get('id');
			$entityManager = $this->getDoctrine()->getManager();
			$job = $entityManager->getRepository(Job::class)->find($id);
			$entityManager->remove($job);
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => 'done',
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/filters", name="ajax_filters")
		 */
		function searchWithFilters(Request $request)
		{
			$cat = $request->request->get('category');
			$location = $request->request->get('location');
			$gender = $request->request->get('gender');
			$experience = $request->request->get('experience');
			$post = $request->request->get('post');
			$time = 0;
			switch ($post) {
				case "Última hora":
					$time = 1;
					break;
				case "Últimas 24 horas";
					$time = 24;
					break;
				case "Últimos 7 días";
					$time = 24 * 7;
					break;
				case "Últimos 14 días";
					$time = 24 * 14;
					break;
				case "Últimos 30 días";
					$time = 24 * 30;
					break;
				default:
					break;
			}
			
			$em = $this->getDoctrine()->getManager();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$category = $em->getRepository(Category::class)->findOneBy(array('name' => $cat));
			$jobs = $em->getRepository(Job::class)->searchCatAndLocation(
				$category,
				$location,
				$gender,
				$experience,
				$time
			);
			
			$t = new \DateTime();
			$newTime = $t->add(\DateInterval::createFromDateString('-'.$time.' hours'));
			if ($time > 0) {
				$i = 0;
				foreach ($jobs as $job) {
					if ($newTime > $job->getDateCreated()) {
						unset($jobs[$i]);
					}
					$i++;
				}
			}
			
			$pagination = $this->get('knp_paginator')->paginate(
				$jobs,
				$request->query->getInt('page', 1),
				10
			);
			$pagination->setTemplate('site/pagination.html.twig');
			$response->setData(
				array(
					'response' => $this->render(
						'site/job/loop.html.twig',
						array(
							'jobs' => $pagination,
						)
					)->getContent(),
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/filters/services", name="ajax_filters_services")
		 */
		function searchWithFiltersServices(Request $request)
		{
			$profesion = $request->request->get('profesion');
			$location = $request->request->get('location');
			$gender = $request->request->get('gender');
			$experience = $request->request->get('experience');
			$post = $request->request->get('post');
			$time = 0;
			switch ($post) {
				case "Última hora":
					$time = 1;
					break;
				case "Últimas 24 horas";
					$time = 24;
					break;
				case "Últimos 7 días";
					$time = 24 * 7;
					break;
				case "Últimos 14 días";
					$time = 24 * 14;
					break;
				case "Últimos 30 días";
					$time = 24 * 30;
					break;
				default:
					break;
			}
			
			$em = $this->getDoctrine()->getManager();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$p = $em->getRepository(Profession::class)->findOneBy(array('name' => $profesion));
			$posts = $em->getRepository(Anouncement::class)->searchByFilters(
				$p,
				$location,
				$gender,
				$experience
			);
			
			$t = new \DateTime();
			$newTime = $t->add(\DateInterval::createFromDateString('-'.$time.' hours'));
			if ($time > 0) {
				$i = 0;
				foreach ($posts as $job) {
					if ($newTime > $job->getDate()) {
						unset($posts[$i]);
					}
					$i++;
				}
			}
			
			$pagination = $this->get('knp_paginator')->paginate(
				$posts,
				$request->query->getInt('page', 1),
				10
			);
			$pagination->setTemplate('site/pagination.html.twig');
			$response->setData(
				array(
					'response' => $this->render(
						'service/loop.html.twig',
						array(
							'jobs' => $pagination,
						)
					)->getContent(),
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/abaut", name="ajax_about")
		 */
		public function aboutAjax(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$resume = $entityManager->getRepository(Resume::class)->findOneBy(
				array('id' => $request->request->get('resume_id'))
			);
			$info = $request->request->get('about');
			if ($info != null) {
				$resume->setAboutMe($request->request->get('about'));
			}
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => $info,
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/skill", name="ajax_skill")
		 */
		public function skillAjax(Request $request)
		{
			$count = $request->request->get('count');
			$info = array();
			for ($i = 1; $i <= $count; $i++) {
				$val = $request->request->get('skill-'.$i);
				if ($val != null) {
					$info[] = $val;
				}
			}
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$user->setSkillArray($info);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => $info,
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/skill/remove", name="ajax_skill_remove")
		 */
		public function removeSkil(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$item = $request->request->get('item');
			$user->remove_skill($item);
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => $item,
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/social", name="ajax_social")
		 */
		public function socialLinks(Request $request)
		{
			$fb = $request->request->get('fb');
			$twitter = $request->request->get('twitter');
			$google = $request->request->get('google');
			$linkedin = $request->request->get('linkedin');
			$printerest = $request->request->get('printerest');
			$instagram = $request->request->get('instagram');
			$behance = $request->request->get('behance');
			$dribbble = $request->request->get('dribbble');
			$github = $request->request->get('github');
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$user->setSocialLinks($this->externalLinkFilter($fb), 'fb');
			$user->setSocialLinks($this->externalLinkFilter($twitter), 'twitter');
			$user->setSocialLinks($this->externalLinkFilter($google), 'google');
			$user->setSocialLinks($this->externalLinkFilter($linkedin), 'linkedin');
			$user->setSocialLinks($this->externalLinkFilter($printerest), 'printerest');
			$user->setSocialLinks($this->externalLinkFilter($instagram), 'instagram');
			$user->setSocialLinks($this->externalLinkFilter($behance), 'behance');
			$user->setSocialLinks($this->externalLinkFilter($dribbble), 'dribbble');
			$user->setSocialLinks($this->externalLinkFilter($github), 'github');
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => "OK",
				)
			);
			
			return $response;
		}
		
		public function externalLinkFilter($url)
		{
			if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
				$url = "http://".$url;
			}
			
			return $url;
		}
		
		/**
		 * @Route("/ajax/metadata/save", name="ajax_metadata_save")
		 */
		public function saveMetadata(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$resume = $entityManager->getRepository(Resume::class)->findOneBy(
				array("id" => $request->request->get('id'))
			);
			$option = $request->request->get('option');
			if ($option == "1") {
				$meta = new Metadata();
				$meta->setResume($resume);
				$meta->setType(constants::METADATA_EDUCATION_DAO);
				$meta->setHeader($request->request->get('title'));
				$meta->setContext($request->request->get('institute'));
				$meta->setExtra($request->request->get('period'));
				$meta->setDescription($request->request->get('description'));
				$meta->setDateCreate(new \DateTime("now"));
				$entityManager->persist($meta);
				$entityManager->flush();
			} else {
				$count = $request->request->get('count');
				for ($i = 1; $i < $count; $i++) {
					if (null !== $up = $entityManager->getRepository(Metadata::class)->find(
							$request->request->get('rid-'.$i)
						)) {
						$up->setHeader($request->request->get('title-'.$i));
						$up->setContext($request->request->get('institute-'.$i));
						$up->setExtra($request->request->get('period-'.$i));
						$up->setDescription($request->request->get('description-'.$i));
						$up->setDateCreate(new \DateTime("now"));
						$entityManager->persist($up);
						$entityManager->flush();
					} else {
						$meta = new Metadata();
						$meta->setResume($resume);
						$meta->setType(constants::METADATA_EDUCATION_DAO);
						$meta->setHeader($request->request->get('title-'.$i));
						$meta->setContext($request->request->get('institute-'.$i));
						$meta->setExtra($request->request->get('period-'.$i));
						$meta->setDescription($request->request->get('description-'.$i));
						$meta->setDateCreate(new \DateTime("now"));
						$entityManager->persist($meta);
						$entityManager->flush();
					}
				}
			}
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => 'done',
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/metadata/remove", name="ajax_metadata_remove")
		 */
		function removeMetadata(Request $request)
		{
			$id = $request->request->get('id');
			$entityManager = $this->getDoctrine()->getManager();
			$meta = $entityManager->getRepository(Metadata::class)->find($id);
			$entityManager->remove($meta);
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => 'done',
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/metadata/expe/save", name="ajax_metadata_exp_save")
		 */
		function saveExperence(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$meta = new Metadata();
			$meta->setDateCreate(new \DateTime("now"));
			$meta->setType(constants::METADATA_EXPERIENCE_DAO);
			$meta->setHeader($request->request->get('title'));
			$meta->setContext($request->request->get('company'));
			$meta->setExtra($request->request->get('period'));
			$meta->setDescription($request->request->get('description'));
			$resume = $entityManager->getRepository(Resume::class)->findOneBy(
				array("id" => $request->request->get('id'))
			);
			$meta->setResume($resume);
			$entityManager->persist($meta);
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => 'done',
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/metadata/porcent/save", name="ajax_metadata_porcent_save")
		 */
		function savePorcent(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			if (!empty($request->request->get('counter'))) {
				$counter = $request->request->get('counter');
				for ($i = 1; $i <= $counter; $i++) {
					$meta = $entityManager->getRepository(Metadata::class)->find($request->request->get('meta_id-'.$i));
					$meta->setHeader($request->request->get('name-'.$i));
					$meta->setContext($request->request->get('porcent-'.$i));
					$entityManager->flush();
				}
				$response = new JsonResponse();
				$response->setData(
					array(
						'response' => 'success',
						'data' => 'done',
					)
				);
				
				return $response;
			} else {
				$meta = new Metadata();
				$meta->setDateCreate(new \DateTime("now"));
				$meta->setType(constants::METADATA_PORCENT_DAO);
				$meta->setHeader($request->request->get('name'));
				$meta->setContext($request->request->get('porcent'));
				$resume = $entityManager->getRepository(Resume::class)->findOneBy(
					array("id" => $request->request->get('id'))
				);
				$meta->setResume($resume);
				$entityManager->persist($meta);
				$entityManager->flush();
				$response = new JsonResponse();
				$response->setStatusCode(200);
				$response->setData(
					array(
						'response' => 'success',
						'data' => 'done',
					)
				);
				
				return $response;
			}
		}
		
		/**
		 * @Route("/ajax/metadata/qualification/save", name="ajax_metadata_qualification_save")
		 */
		function saveQualification(Request $request)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$meta = new Metadata();
			$meta->setDateCreate(new \DateTime("now"));
			$meta->setType(constants::METADATA_QUALIFICATION_DAO);
			$meta->setHeader($request->request->get('name'));
			$resume = $entityManager->getRepository(Resume::class)->findOneBy(
				array("id" => $request->request->get('id'))
			);
			$meta->setResume($resume);
			$entityManager->persist($meta);
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => 'done',
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/bookmark", name="ajax_bookmark")
		 */
		function bookMark(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$id = $request->request->get('id');
			if (false !== $key = array_search($id, array_values($user->getBookmarked()), true)) {
				$user->removeBookMarked($key);
			} else {
				$user->addBookMarket($id);
			}
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => $request->request->get('id'),
				)
			);
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/applied", name="ajax_applied")
		 */
		function applied(Request $request)
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$id = $request->request->get('id');
			$new = false;
			$entityManager = $this->getDoctrine()->getManager();
			$job = $entityManager->getRepository(Job::class)->find($id);
			if (false !== $key = array_search($id, array_values($user->getApplied()), true)) {
				$user->removeApplied($key);
				$user->removeJobAppiled($job);
				$job->removeUser($user);
				$job = $entityManager->getRepository(Job::class)->find($id);
				$this->notificate(
					constants::NOTIFICATIONS_JOB_APPLIED_CANCEL,
					"Cancelaste tu propuesta: ".$job->getTitle(),
					$user
				);
			} else {
				$user->addApplied($id);
				$user->addJobAppiled($job);
				$job->addUser($user);
				$new = true;
				$job = $entityManager->getRepository(Job::class)->find($id);
				$this->notificate(
					constants::NOTIFICATIONS_JOB_APPLIED_OK,
					"Aplicaste al trabajo: ".$job->getTitle(),
					$user
				);
				$this->notificate(
					constants::NOTIFICATIONS_JOB_APPLIED_ADMIN,
					"El usuario ".$user->getUsername()." ha aplicado a su publicación:".$job->getTitle(),
					$job->getUser()
				);
			}
			$entityManager->flush();
			$response = new JsonResponse();
			$response->setStatusCode(200);
			$response->setData(
				array(
					'response' => 'success',
					'data' => $new,
				)
			);
			
			return $response;
		}
		
		function notificate($type, $context, $user)
		{
			$entityManager = $this->getDoctrine()->getManager();
			$notification = new Notification();
			$notification->setDate(new \DateTime());
			$notification->setType($type);
			$notification->setContext($context);
			$notification->setUser($user);
			$notification->setActive(true);
			$entityManager->persist($notification);
			$entityManager->flush();
		}
		
		/**
		 * @Route("/mail/sender", name="mail_sender")
		 */
		public function sendEmail(Request $request, \Swift_Mailer $mailer)
		{
			try {
				$message = (new \Swift_Message('Notificación'))
					->setFrom('emplearecuador@gmail.com')
					->setBody(
						$this->renderView(
							'mail/contact.html.twig',
							[
								'remit' => $request->request->get('remit'),
								'body' => $request->request->get('body'),
								'email' => $request->request->get('email'),
							]
						),
						'text/html'
					)
					->setTo($request->request->get('destinate'));
				$mailer->send($message);
				$response = new JsonResponse();
				$response->setStatusCode(200);
				$response->setData(
					array(
						'response' => 'success',
						'data' => 'success',
					)
				);
			} catch (\Exception $exception) {
				$response = new JsonResponse();
				$response->setStatusCode(423);
				$response->setData(
					array(
						'response' => 'error',
						'data' => 'error',
					)
				);
			}
			
			return $response;
		}
		
		/**
		 * @Route("/ajax/contrata", name="ajax_contrata")
		 */
		public function contrataAction(Request $request, \Swift_Mailer $mailer)
		{
			$id = $request->request->get('id');
			$em = $this->getDoctrine()->getManager();
			$service = $em->getRepository(Anouncement::class)->find($id);
			$serviceCreator = $service->getUser();
			$serviceCandidate = $this->get('security.token_storage')->getToken()->getUser();
			if ($serviceCandidate->getServicesRequest()!= null && in_array($service->getId(), $serviceCandidate->getServicesRequest()) ){
				$response = new JsonResponse();
				$response->setStatusCode(423);
				$response->setData(
					array(
						'response' => 'error',
						'data' => 'error',
					)
				);
				return $response;
			}
			try {
				$message = (new \Swift_Message('Notificación'))
					->setFrom('emplearecuador@gmail.com')
					->setBody(
						$this->renderView(
							'mail/contrata.html.twig',
							[
								'serviceCreator' => $serviceCreator,
								'serviceCandidate' => $serviceCandidate,
							]
						),
						'text/html'
					)
					->setTo($serviceCreator->getEmail());
				$mailer->send($message);
				$this->notificate(
					constants::NOTIFICATIONS_JOB_APPLIED_OK,
					"Aplicaste al servicio: ".$service->getTitle(),
					$serviceCandidate
				);
				$this->notificate(
					constants::NOTIFICATIONS_JOB_APPLIED_ADMIN,
					"El usuario ".$serviceCandidate->getUsername()." ha aplicado a su publicación:".$service->getTitle(),
					$service->getUser()
				);
				$serviceCandidate->addServiceRequest($service->getId());
				$response = new JsonResponse();
				$response->setStatusCode(200);
				$response->setData(
					array(
						'response' => 'success',
						'data' => 'success',
					)
				);
				$em->flush();
				return $response;
			} catch (\Exception $exception) {
				$response = new JsonResponse();
				$response->setStatusCode(423);
				$response->setData(
					array(
						'response' => 'error',
						'data' => 'error',
					)
				);
				
				return $response;
			}
			
		}
	}