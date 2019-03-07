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
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\JobType;
use Symfony\Component\Validator\Constraints\Date;

class JobController extends AbstractController
{
    /**
     * @Route("/job/new/", name="job_new")
     */
    public function jobNew(Request $request)
    {
        $post = new Job();
        $form = $this->createForm(JobType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $request->get('my_radio');
            switch ($payment) {
                case constants::PAYMENT_FREE:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+15 day')));
                    break;
                case constants::PAYMENT_BASIC:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+30 day')));
                    break;
                case constants::PAYMENT_PYME:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+30 day')));
                    break;
                case constants::PAYMENT_PLUS:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+45 day')));
                    break;
                default:
                    echo "FAIL";die;
                    break;
            }
            if ($form->get('date')->getData() < new \DateTime()) {
                $form->get('date')->addError(new FormError("La fecha de debe ser mayor que la fecha acual"));
                return $this->render('site/job/job.html.twig', [
                    'form' => $form->createView(),
                    'notifications' => $this->loadNotifications(),
                ]);
            } elseif ($form->get('date')->getData() > new \DateTime()) {
                $post->setStatus(constants::JOB_STATUS_PENDING);
            }else{
                $post->setStatus(constants::JOB_STATUS_ACTIVE);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $post->setUser($this->get('security.token_storage')->getToken()->getUser());
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
            return $this->redirectToRoute('homepage');
        }
        return $this->render('site/job/job.html.twig', [
            'form' => $form->createView(),
            'notifications' => $this->loadNotifications(),
        ]);
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

    /**
     * @Route("/job/list/", name="job_list")
     */
    public function jobList(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->findAll();
        return $this->render('site/job/list.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'jobs' => $jobs,
                'notifications' => $this->loadNotifications()
            ]);
    }

    /**
     * @Route("/job/{id}/", name="job_show")
     */
    public function jobShow($id)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository(Job::class)->find($id);
        return $this->render('site/job/view.html.twig', [
            'job' => $job,
            'notifications' => $this->loadNotifications()
        ]);
    }

    /**
     * @Route("/job/manage/{id}/", name="job_manage")
     */
    public function manage($id)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $jobs = $em->getRepository(Job::class)->finJobByUser($user);
        return $this->render('user/employer/manage_job.html.twig', [
            'jobs' => $jobs,
            'notifications' => $this->loadNotifications()
        ]);
    }
}