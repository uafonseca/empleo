<?php

namespace App\Controller;

use App\constants;
use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Notification;
use App\Form\JobType;
use App\Form\ServiceJobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends Controller
{
    /**
     * @Route("/service", name="service")
     */
    public function index()
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
	/**
	 * @Route("/service/new", name="service_new")
	 * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
	 * @IsGranted("ROLE_USER")
	 */
	public function serviceNew(Request $request)
	{
		$post = new Job();
		$form = $this->createForm(ServiceJobType::class, $post);
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
		$entityManager = $this->getDoctrine()->getManager();
		$post->setCategory($entityManager->getRepository(Category::class)->findAll()[0]);
		$post->setDate(new \DateTime("now"));
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$payment = $request->get('my_radio');
			switch ($payment) {
				case constants::PAYMENT_FREE:
					$currentUser->setPackage(constants::PAYMENT_FREE);
					$post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_FREE_DAYS.' day')));
					break;
				case constants::PAYMENT_BASIC:
					$currentUser->setPackage(constants::PAYMENT_BASIC);
					$post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_BASIC_DAYS.' day')));
					break;
				case constants::PAYMENT_PYME:
					$currentUser->setPackage(constants::PAYMENT_PYME);
					$post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_PYME_DAYS.' day')));
					break;
				case constants::PAYMENT_PLUS:
					$currentUser->setPackage(constants::PAYMENT_PLUS);
					$post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_PLUS_DAYS.' day')));
					break;
				default:
					echo "FAIL";die;
					break;
			}
			$post->setStatus(constants::JOB_STATUS_ACTIVE);
			$post->setIsService(true);
			$entityManager->flush();
			$post->setUser($currentUser);
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
			return $this->redirectToRoute('service_manage');
		}
		return $this->render('service/new.html.twig', [
			'form' => $form->createView(),
			'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
		]);
	}
	/**
	 * @Route("/service/manage", name="service_manage")
	 */
	public function manage(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$jobs = $em->getRepository(Job::class)->finServicesByUser($user);
		$pagination  = $this->get('knp_paginator')->paginate(
			$jobs,
			$request->query->getInt('page', 1),
			5);
		$pagination->setTemplate('site/pagination.html.twig');
		return $this->render('service/manage.html.twig', [
			'jobs' => $pagination,
			'notifications' => $this->container->get('app.service.helper')->loadNotifications()
		]);
	}
	/**
	 * @Route("/service/list", name="service_list")
	 */
	public function serviceList(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$jobs = $em->getRepository(Job::class)->listServices();
		$pagination  = $this->get('knp_paginator')->paginate(
			$jobs,
			$request->query->getInt('page', 1),
			5);
		$pagination->setTemplate('site/pagination.html.twig');
		return $this->render('service/list.html.twig', [
			'jobs' => $pagination,
			'notifications' => $this->container->get('app.service.helper')->loadNotifications()
		]);
	}
}
