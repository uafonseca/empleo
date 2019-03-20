<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 8/2/2019
 * Time: 18:26
 */

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Metadata;
use App\Entity\Notification;
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

class mainController extends Controller
{

//    /**
//     * @Route("/mail",name="mail")
//     */
//    public function mailView(){
//        return $this->render('mail/register.html.twig',['user'=>$this->get('security.token_storage')->getToken()->getUser()]);
//    }

    /**
     * @Route("/",name="homepage")
     */
    public function index(): Response

    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('site/job/index.html.twig', [
            'notifications' => $this->loadNotifications(),
            'jobs' => $em->getRepository(Job::class)->findAll(),
            'locations'=> $em->getRepository(Job::class)->Locations(),
            'categorys'=>$em->getRepository(Category::class)->categoryesCount(),
            'citys'=>$em->getRepository(Job::class)->Cityes(),
            'company'=>$em->getRepository(Job::class)->Company(),
            'works'=>count($em->getRepository(Job::class)->jobsByStatus(constants::JOB_STATUS_ACTIVE)),
            'candidates'=>count($em->getRepository(User::class)->findByRole(\FOS\UserBundle\Model\User::ROLE_DEFAULT)),
            'cv'=>count($em->getRepository(Resume::class)->findAll()),
            'entity'=>count($em->getRepository(User::class)->findByRole('ROLE_ADMIN')),
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
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return new Response(
            '<html><body>Contact </body></html>'
        );
    }

    /**
     * @Route("/dashboard", name="dashboard")
     * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function dashboard()
    {
        $notifications = $this->loadNotifications();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $em = $this->getDoctrine()->getManager();
            $public = $em->getRepository(Job::class)->countJob($user);
            return $this->render('user/employer/dashboard.html.twig', array(
                'notifications' => $notifications,
                'public'=>$public,
                'requests'=>$em->getRepository(Job::class)->requests($user),
            ));
        } else {
            return $this->render('user/dashboard.html.twig', array(
                'notifications' => $notifications,
            ));
        }
    }


    /**
     * @Route("/dashboard/edit", name="dashboard_edit")
     */
    public function edit_profile(Request $request)
    {
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
            return $this->render('user/employer/edit_profile.html.twig',
                array(
                    'form' => $form->createView(),
                    'notifications' => $this->loadNotifications(),
                ));
        } else {
            return $this->render('user/edit_profile.html.twig',
                array(
                    'form' => $form->createView(),
                    'notifications' => $this->loadNotifications(),
                ));
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
     */
    public function resume()
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $metas = $em->getRepository(Metadata::class)->findBy(array("resume" => $user->getResume()));
        $cv = $user->getResume()->getCv();
        $cart = $user->getResume()->getCart();
        return $this->render('user/resume.html.twig',
            array(
                'resume' => $user->getResume(),
                'notifications' => $this->loadNotifications(),
                'cv' => $cv,
                'cart' => $cart,
                'metas' => $metas,
            ));
    }

    /**
     * @Route("/dashboard/resume/edit", name="dashboard_resume_edit")
     */
    public function resumeEdit(Request $request)
    {
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
        return $this->render('user/resume_edit.html.twig',
            array(
                'resume' => $resume,
                'notifications' => $this->loadNotifications(),
                'form_resume' => $form->createView(),
                'form_files' => $formFiles->createView(),
                'metas' => $metas,
            ));
    }

    /**
     * @Route("/dashboard/bookmarked", name="dashboard_bookmarked")
     */
    public function bookMarked()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $marked = $user->getBookmarked();
        $jobs = array();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($marked as $id) {
            $jobs[] = $entityManager->getRepository(Job::class)->find($id);
        }
        return $this->render('user/mark.html.twig',
            array(
                'jobs' => $jobs,
                'notifications' => $this->loadNotifications(),

            ));
    }

    /**
     * @Route("/dashboard/applied", name="dashboard_applied")
     */
    public function applied()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $marked = $user->getApplied();
        $jobs = array();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($marked as $id) {
            $jobs[] = $entityManager->getRepository(Job::class)->find($id);
        }
        return $this->render('user/applied.html.twig',
            array(
                'jobs' => $jobs,
                'notifications' => $this->loadNotifications(),

            ));
    }

    /**
     * @Route("/candidates", name="candidates")
     */
    public function candidates()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        $i = 0;
        foreach ($users as $user){
            if(!$user->getCandidate()){
                unset($users[$i]);
            }
            $i++;
        }
        $path = $this->getParameter('app.path.user_cv') . '/';
        return $this->render('site/candidate.html.twig',
            array(
                'candidates' => $users,
                'notifications' => $this->loadNotifications(),
                'url'=>$path,
            ));
    }
    /**
     * @Route("/manage/candidates", name="manage_candidates")
     */
    public function manageCandidates(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findCandidatesList($user = $this->get('security.token_storage')->getToken()->getUser()->getId());
        $i = 0;
        foreach ($users as $user){
            if(!$user->getCandidate()){
                unset($users[$i]);
            }
            $i++;
        }
        $pagination  = $this->get('knp_paginator')->paginate(
            $users,
            $request->query->getInt('page', 1),
            10);
        $pagination->setTemplate('site/pagination.html.twig');
        $path = $this->getParameter('app.path.user_cv') . '/';
        return $this->render('user/employer/candidate.html.twig',
            array(
                'candidates' => $pagination,
                'notifications' => $this->loadNotifications(),
                'url'=>$path,
            ));
    }
    /**
     * @Route("/manage/mail/write", name="manage_mail_write")
     */
    public function writeEmail()
    {
        return $this->render('mail/send.html.twig');
    }
    /**
    *@Route("/candidate/{id}/detail", name="canditate_detail")
    */
    public function candidateDetails($id)
    {
        $em = $this->getDoctrine()->getManager();
        $canditate =$em->getRepository(User::class)->find($id);
        return $this->render('user/employer/candidate_detail.html.twig',
            array(
                'notifications'=>$this->loadNotifications(),
                'candidate'=>$canditate,
            ));
    }

    /**
     *@Route("/dowload/cv/{name}", name="dowload_cv")
     */
    public function dowloadCv($name)
    {
        $base = $this->getParameter('kernel.project_dir') . '/public/';
        $path = $this->getParameter('app.path.user_cv') . '/';
        $response = new BinaryFileResponse($base.$path.$name);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$name);
        return $response;
    }
}
