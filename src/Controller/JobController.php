<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 2/18/2019
 * Time: 2:38 PM
 */

namespace App\Controller;

use App\constants;
use App\Entity\Job;
use App\Entity\Notification;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\JobType;

class JobController extends Controller
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
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_FREE_DAYS.' day')));
                    break;
                case constants::PAYMENT_BASIC:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_BASIC_DAYS.' day')));
                    break;
                case constants::PAYMENT_PYME:
                    $post->setExpiredDate($post->getDate()->add(\DateInterval::createfromdatestring('+'.constants::PAYMENT_PYME_DAYS.' day')));
                    break;
                case constants::PAYMENT_PLUS:
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
            return $this->redirectToRoute('job_manage',['id'=>$this->get('security.token_storage')->getToken()->getUser()->getId()]);
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
     * @Route("/search",name="search")
     */
    public function search(Request $request){
        $keywords = $request->request->get('keywords');
        $location = $request->request->get('location');
        $em = $this->getDoctrine()->getManager();
        $pagination  = $this->get('knp_paginator');
        if(empty($keywords) && empty($location)){
            $pagination->paginate(
                $em->getRepository(Job::class)->findAllHome(),
                $request->query->getInt('page', 1),
                10);
            return $this->redirectToRoute('homepage');
        }else{
            $pagination->paginate(
                $em->getRepository(Job::class)->search($keywords,$location),
                $request->query->getInt('page', 1),
                10);
        }
        return $this->render('site/job/list.html.twig', array(
            'jobs' => $pagination->paginate($em->getRepository(Job::class)->search($keywords,$location),$request->query->getInt('page', 1),10),
            'notifications' => $this->loadNotifications(),
            'search'=>1,
            ));
    }

    /**
     * @Route("/job/list/", name="job_list")
     */
    public function jobList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->findAll();
        $em->getRepository(Job::class)->expired();
        $pagination  = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            10);
        $pagination->setTemplate('site/pagination.html.twig');
        return $this->render('site/job/list.html.twig',
            [
                'jobs' => $pagination,
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
    public function manage($id,Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $jobs = $em->getRepository(Job::class)->finJobByUser($user);
        $pagination  = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            5);
        $pagination->setTemplate('site/pagination.html.twig');
        return $this->render('user/employer/manage_job.html.twig', [
            'jobs' => $pagination,
            'notifications' => $this->loadNotifications()
        ]);
    }
}