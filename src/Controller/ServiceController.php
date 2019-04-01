<?php

namespace App\Controller;

use App\constants;
use App\Entity\Job;
use App\Entity\Notification;
use App\Form\JobType;
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
		$form = $this->createForm(JobType::class, $post);
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
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
			if ($form->get('date')->getData() < new \DateTime()) {
				$form->get('date')->addError(new FormError("La fecha de debe ser mayor que la fecha acual"));
				return $this->render('site/job/job.html.twig', [
					'form' => $form->createView(),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				]);
			} elseif ($form->get('date')->getData() > new \DateTime()) {
				$post->setStatus(constants::JOB_STATUS_PENDING);
			}else{
				$post->setStatus(constants::JOB_STATUS_ACTIVE);
			}
			$post->setIsService(true);
			$entityManager = $this->getDoctrine()->getManager();
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
			return $this->redirectToRoute('job_manage',['id'=>$this->get('security.token_storage')->getToken()->getUser()->getId()]);
		}
		return $this->render('service/new.html.twig', [
			'form' => $form->createView(),
			'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
		]);
	}
}
