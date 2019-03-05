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
use App\Entity\Resume;
use App\Entity\User;
use App\Form\ResumeFilesType;
use App\Form\ResumeType;
use App\Form\UserFullyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\constants;

class mainController extends AbstractController
{
    /**
     * @Route("/",name="homepage")
     */
    public function index(AuthenticationUtils $authenticationUtils,\Swift_Mailer $mailer ): Response
    {

//        $message = (new \Swift_Message('Hello Email'))
//            ->setFrom('uafonseca@uci.cu')
//            ->setTo('malenaah@estudiantes.uci.cu')
//            ->setBody("adoasdjkashdjhasjkdhaskj");
//
//
//        $mailer->send($message);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->findAll();
        return $this->render('site/job/index.html.twig', [
            'notifications'=>$this->loadNotifications(),
            'jobs'=>$jobs,
        ]);
    }
    public function loadNotifications(){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(null != $user){
            $em = $this->getDoctrine()->getManager();
            $notifications =  $em->getRepository(Notification::class)->findBy(
                array(
                    'user'=>$user,
                    'active'=>true,
                ),
                array(
                    'date'=>'DESC',
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
     */
    public function dashboard()
    {
        $notifications = $this->loadNotifications();
        return $this->render('user/dashboard.html.twig',array(
            'notifications'=>$notifications,
        ));
    }
    /**
     * @Route("/dashboard/edit", name="dashboard_edit")
     */
    public function edit_profile(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createForm(UserFullyType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $notification =  new Notification();
            $notification->setDate(new \DateTime());
            $notification->setType(constants::NOTIFICATION_PROFILE_UPDATE);
            $notification->setContext("Su perfil se ha actualizado correctamente");
            $notification->setUser($user);
            $notification->setActive(true);
            $entityManager->persist($notification);
            $entityManager->flush();
        }
        $notifications = $this->loadNotifications();
        return $this->render('user/edit_profile.html.twig',
            array(
                'form'=>$form->createView(),
                'notifications'=>$notifications,
            ));
    }
    /**
     * @Route("/ajax", name="ajax")
     */
    public function deleteNotification($id){
        echo json_encode($id);
    }
    /**
     * @Route("/dashboard/resume", name="dashboard_resume")
     */
    public function resume(){

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        if($user->getResume()== null){
            $resume = new Resume();
            $resume->setUser($user);
            $em->persist($resume);
            $em->flush();
        }
        $metas = $em->getRepository(Metadata::class)->findBy(array("resume"=>$user->getResume()));
        $cv = null;
        $cart = null;
        if(null !== $user->getResume()->getCv() )
            $cv = $this->getParameter('app.path.user_cv').'/'.$user->getResume()->getCv();
        if($user->getResume()->getCart() != null)
            $cart = $this->getParameter('app.path.user_cv').'/'.$user->getResume()->getCart();
        return $this->render('user/resume.html.twig',
            array(
                'resume'=>$user->getResume(),
                'notifications'=>$this->loadNotifications(),
                'cv'=>$cv,
                'cart'=>$cart,
                'metas'=>$metas,
            ));
    }
    /**
     * @Route("/dashboard/resume/edit", name="dashboard_resume_edit")
     */
    public function resumeEdit(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        if($user->getResume()== null){
            $resume = new Resume();
            $resume->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($resume);
            $em->flush();
        }
        $resume = $entityManager->getRepository(Resume::class)->findOneBy(array('user'=>$user,));
        $metas = $entityManager->getRepository(Metadata::class)->findBy(array("resume"=>$resume));
        $form = $this->createForm(ResumeType::class,$resume);
        $formFiles = $this->createForm(ResumeFilesType::class,$resume);
        $formFiles->handleRequest($request);
        if($formFiles->isSubmitted() && $formFiles->isValid()){
            $resume->setUpdatedAt(new \DateTime("now"));
            $entityManager->flush();
            $notification =  new Notification();
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
                'resume'=>$resume,
                'notifications'=>$this->loadNotifications(),
                'form_resume'=>$form->createView(),
                'form_files'=>$formFiles->createView(),
                'notifications'=>$this->loadNotifications(),
                'metas'=>$metas,
            ));
    }
    /**
     * @Route("/dashboard/bookmarked", name="dashboard_bookmarked")
     */
    public function bookMarked(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $marked = $user->getBookmarked();
        $jobs = array();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($marked as $id){
            $jobs[] = $entityManager->getRepository(Job::class)->find($id);
        }
        return $this->render('user/mark.html.twig',
            array(
                'jobs' => $jobs,
                'notifications'=>$this->loadNotifications(),

            ));
    }
    /**
     * @Route("/dashboard/applied", name="dashboard_applied")
     */
    public function applied(Request $request){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $marked = $user->getApplied();
        $jobs = array();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($marked as $id){
            $jobs[] = $entityManager->getRepository(Job::class)->find($id);
        }
        return $this->render('user/applied.html.twig',
            array(
                'jobs' => $jobs,
                'notifications'=>$this->loadNotifications(),

            ));
    }
    /**
     * @Route("/candidates", name="candidates")
     */
    public function candidates(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $candidates = $entityManager->getRepository(User::class)->findAll();
        return $this->render('site/candidate.html.twig',
            array(
                'candidates' => $candidates,
                'notifications'=>$this->loadNotifications(),

            ));
    }

}