<?php

/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 8/2/2019
 * Time: 18:26
 */

namespace App\Controller;

use App\Datatable\emailsDatatable;
use App\Entity\Anouncement;
use App\Entity\ContactMessage;
use App\Entity\Job;
use App\Entity\Metadata;
use App\Entity\Notification;
use App\Entity\Payment;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\Policy;
use App\Entity\Resume;
use App\Entity\StaticPage;
use App\Entity\User;
use App\Entity\UserJobMeta;
use App\Form\ContactMessageFormType;
use App\Form\ResumeFilesType;
use App\Form\ResumeType;
use App\Form\UserFullyEmployerType;
use App\Form\UserFullyType;
use App\Service\Mailer;
use App\Repository\CompanyRepository;
use App\Repository\ContactMessageRepository;
use App\Repository\UserJobMetaRepository;
use App\Repository\UserRepository;
use App\Service\CategoryService;
use App\Service\CompanyService;
use App\Service\JobService;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\constants;
use App\Entity\ClientTransaction;
use App\Entity\Slide;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


/**
 * Class mainController
 * @package App\Controller
 */
class mainController extends Controller
{

    /** @var CategoryService */
    private $categoryService;

    /** @var JobService */
    private $jobService;

    /** @var CompanyService */
    private $companyService;

    /** @var Mailer */
    protected $mailer;

    /** @var SessionInterface */
    private $session;

    /** @var DatatableFactory */
    private $datatableFactory;

    /** @var DatatableResponse */
    private $datatableResponse;


    /**
     * mainController constructor.
     * @param CategoryService $categoryService
     * @param JobService $jobService
     * @param CompanyService $companyService
     * @param Mailer $mailer
     * @param SessionInterface $session
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $datatableResponse
     */
    public function __construct(
        CategoryService $categoryService,
        JobService $jobService,
        CompanyService $companyService,
        Mailer $mailer,
        SessionInterface $session,
        DatatableFactory $datatableFactory,
        DatatableResponse $datatableResponse
    ) {
        $this->categoryService = $categoryService;
        $this->jobService = $jobService;
        $this->companyService = $companyService;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }


    /**
     * @Route("/mail",name="mail")
     */
    public function mailView()
    {
        return $this->render('mail/contrata.html.twig');
    }

    public function verificateUser(AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            $fileSystem = new Filesystem();
            try {
                $base = $this->getParameter('kernel.project_dir') . '/public';
                $path = $this->getParameter('app.path.user_images');
                $user = $this->get('security.token_storage')->getToken()->getUser();
                $dir = $base . $path . '/_files_' . $user->getUsername();
                if (!$fileSystem->exists($dir)) {
                    $fileSystem->mkdir($dir);
                    if ($fileSystem->exists($base . $path . '/' . $user->getImage())) {
                        $fileSystem->copy($base . $path . '/' . $user->getImage(), $dir . '/' . $user->getImage());
                        $fileSystem->remove($base . $path . '/' . $user->getImage());
                    }
                }
            } catch (IOExceptionInterface $exception) {
            }
            if (!$this->container->get('app.service.checker')->isUserValid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/pricing",name="pricing_page")
     */
    public function pricing(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $packagesJobs = $this->jobService->findAviableJobsPacks($this->getUser());
        $packagesServices = $em->getRepository(PaymentForServices::class)->findAll();

        $type = $request->query->get('type');

        if (!isset($type)) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $type = 'job';
            } else {
                $type = 'service';
            }
        }
        $_error = $this->session->get('_error');
        $_error_ = $this->session->get('_error_');
        if ($_error) {
            $this->session->remove('_error');
        }
        if ($_error_) {
            $this->session->remove('_error_');
        }
        return $this->render(
            'site/pricing.html.twig',
            [
                'notifications' => $this->loadNotifications(),
                'packagesJobs' => $packagesJobs,
                'packagesServices' => $packagesServices,
                'type' => $type,
                '_error' => $_error,
                '_error_' => $_error_
            ]
        );
    }


    /**
     * @Route("/checkout/{packId}/{type}",name="checkout")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function checkout($packId, $type)
    {
        $em = $this->getDoctrine()->getManager();
        $transaction = new ClientTransaction();
        $transaction
            ->setCreatedAt(new DateTime('now'))
            ->setConfirmed(false)
            ->setUser($this->getUser())
            ->setCode(md5(uniqid(rand(), true)));
        if ($type == 'job') {
            /** @var PaymentForJobs $package */
            $package = $em->getRepository(PaymentForJobs::class)->find($packId);
        } else {
            /** @var PaymentForServices $package */
            $package = $em->getRepository(PaymentForServices::class)->find($packId);
        }
        $em->persist($transaction);
        $em->flush();
        return $this->render(
            'site/checkout.html.twig',
            [
                'notifications' => $this->loadNotifications(),
                'package' => $package,
                'type' => $type,
                'transaction' => $transaction
            ]
        );
    }

    public function updateJobsFiles()
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->findAll();
        $base = $this->getParameter('kernel.project_dir') . '/public';
        $path = $this->getParameter('app.path.company_images');
        $fileSystem = new Filesystem();
        foreach ($jobs as $job) {
            $dir = $base . $path . '/_user_' . $job->getUser()->getId();
            try {
                $fileSystem->mkdir($dir);
                if ($fileSystem->exists($base . $path . '/' . $job->getImage())) {
                    $fileSystem->copy($base . $path . '/' . $job->getImage(), $dir . '/' . $job->getImage());
                    $fileSystem->remove($base . $path . '/' . $job->getImage());
                }
            } catch (IOExceptionInterface $exception) {
                echo $exception->getMessage();
                die;
            }
        }
    }

    /**
     * @Route("/",name="homepage")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function index(AuthorizationCheckerInterface $authChecker): Response
    {
        $verified = $this->verificateUser($authChecker);
        $em = $this->getDoctrine()->getManager();
        $this->container->get('app.service.checker')->checkJobs();

        $slides = $em->getRepository(Slide::class)->findAll();

        return $this->render(
            'site/job/index.html.twig',
            [
                'verificated_acount' => $verified,
                'notifications' => $this->loadNotifications(),
                'jobs' => $em->getRepository(Job::class)->findBy(
                    array('status' => constants::JOB_STATUS_ACTIVE),
                    array('dateCreated' => 'desc'),
                    10
                ),
                'services' => $em->getRepository(Anouncement::class)->findBy([
                    'status' => constants::JOB_STATUS_ACTIVE,

                ], [
                    'date' => 'desc'
                ], 10),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
                'categorys' => $this->jobService->findByAllCategory(),
                'citys' => $this->container->get('app.service.helper')->loadCityes(),
                'company' => $this->companyService->findActives(),
                'entity' => count($em->getRepository(User::class)->findByRole('ROLE_ADMIN')),
                'slides' => $slides
            ]
        );
    }

    /**
     * @param CompanyRepository $repository
     * @return Response
     *
     * @Route("/companies", name="listado_companias")
     */
    public function companies(CompanyRepository $repository)
    {
        return $this->render('site/job/companies.html.twig', [
            'companies' => $repository->findActives(),
            'notifications' => $this->loadNotifications(),
            'locations' => $this->container->get('app.service.helper')->loadLocations(),
            'categorys' => $this->jobService->findByAllCategory(),
            'citys' => $this->container->get('app.service.helper')->loadCityes(),
            'company' => $this->companyService->findActives(),
        ]);
    }


    /**
     * @return Response
     *
     * @Route ("/servicces", name="load_services_request")
     */
    public function serviciosSolicidatos()
    {
        /** @var User $user */
        $user = $this->getUser();
        $ids = $user->getServicesRequest();

        $em = $this->getDoctrine()->getManager();
        $services = [];

        foreach ($ids as $id) {
            $services[] = $em->getRepository(Anouncement::class)->find($id);
        }

        return $this->render('site/job/services.html.twig', [
            'services' => $services,
            'notifications' => $this->loadNotifications(),
        ]);
    }

    /**
     * @return object[]|null
     */
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
     * @Route("/terms", name="site_policy")
     * @param AuthorizationCheckerInterface $authChecker
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function policy(AuthorizationCheckerInterface $authChecker)
    {
        $em = $this->getDoctrine()->getManager();
        $verificated = $this->verificateUser($authChecker);
        $terms = $em->getRepository(Policy::class)->load();
        if (null == $terms) {
            return $this->redirectToRoute('homepage');
        }
        return $this->render(
            'site/policy.html.twig',
            array(
                'verificated_acount' => $verificated,
                'notifications' => $this->loadNotifications(),
                'terms' => $terms,
            )
        );
    }


    /**
     * @Route("/dashboard", name="dashboard")
     * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function dashboard(AuthorizationCheckerInterface $authChecker)
    {
        /** @var User $user */
        $user = $this->getUser();
        $verificated = $this->verificateUser($authChecker);
        $paymentMetadata = [
            'jobs' => $this->jobService->getCurrentJobPackage($user),
            'services' => $this->jobService->getCurrentServicesPackage($user)
        ];
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $em = $this->getDoctrine()->getManager();
            $public = $em->getRepository(Job::class)->countJob($user);

            return $this->render(
                'user/employer/dashboard.html.twig',
                array(
                    'notifications' => $this->loadNotifications(),
                    'public' => $public,
                    'requests' => $em->getRepository(Job::class)->requests($user),
                    'paymentMetadata' => $paymentMetadata
                )
            );
        } else {
            return $this->render(
                'user/dashboard.html.twig',
                array(
                    'verificated_acount' => $verificated,
                    'notifications' => $this->loadNotifications(),
                    'paymentMetadata' => $paymentMetadata
                )
            );
        }
    }


    /**
     * @param Request $request
     * @param AuthorizationCheckerInterface $authChecker
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/profile/edit", name="dashboard_edit")
     */
    public function edit_profile(Request $request, AuthorizationCheckerInterface $authChecker)
    {
        $verified = $this->verificateUser($authChecker);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $is_admin = in_array('ROLE_ADMIN', $user->getRoles());
        $entityManager = $this->getDoctrine()->getManager();
        if ($is_admin) {
            $form = $this->createForm(UserFullyEmployerType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
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
        $paymentMetadata = [
            'jobs' => $this->jobService->getCurrentJobPackage($user),
            'services' => $this->jobService->getCurrentServicesPackage($user)
        ];
        if ($is_admin) {
            return $this->render(
                'user/employer/edit_profile.html.twig',
                array(
                    'verificated_acount' => $verified,
                    'form' => $form->createView(),
                    'notifications' => $this->loadNotifications(),
                    'paymentMetadata' => $paymentMetadata
                )
            );
        } else {
            $active = $this->getDoctrine()->getRepository(PaymentForServicesMetadata::class)->checkUser($this->getUser());


            return $this->render(
                'user/edit_profile.html.twig',
                array(
                    'form' => $form->createView(),
                    'notifications' => $this->loadNotifications(),
                    'paymentMetadata' => $paymentMetadata,
                    'activePack' => $active != null,
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
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
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
                'user' => $this->getUser()
            )
        );
    }

    /**
     * @Route("/dashboard/resume/edit", name="dashboard_resume_edit")
     * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
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
        /** @var Resume $resume */
        $resume = $entityManager->getRepository(Resume::class)->findOneBy(array('user' => $user,));
        $metas = $entityManager->getRepository(Metadata::class)->findBy(array("resume" => $resume));
        $form = $this->createForm(ResumeType::class, $resume);
        $haveCv = false;
        $haveCart = false;

        if ($resume->getCvFile()) {
            $haveCv = true;
        }
        if ($resume->getCartFile()) {
            $haveCart = false;
        }

        $formFiles = $this->createForm(ResumeFilesType::class, $resume, [
            'cv' => $haveCv,
            'cart' => $haveCart
        ]);
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
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
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
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function applied(AuthorizationCheckerInterface $authChecker)
    {
        $verificated = $this->verificateUser($authChecker);
        /** @var User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $jobs = $user->getJobAppiled();
        //        $jobs = array();
        //        $entityManager = $this->getDoctrine()->getManager();
        //        foreach ($marked as $id) {
        //            $jobs[] = $entityManager->getRepository(Job::class)->find($id);
        //        }

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
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     */
    public function candidates(AuthorizationCheckerInterface $authChecker)
    {
        $verificated = $this->verificateUser($authChecker);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $canditatesID = $user->getCandidates();
        $users = array();
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($canditatesID as $item) {
            if ($user->getId() != $item) {
                array_push($users, $entityManager->getRepository(User::class)->find($item));
            }
        }
        return $this->render(
            'site/candidate.html.twig',
            array(
                'verificated_acount' => $verificated,
                'candidates' => $users,
                'notifications' => $this->loadNotifications(),
            )
        );
    }

    /**
     * @Route("/manage/candidates", name="manage_candidates")
     * Require IS_AUTHENTICATED_FULLY for *every* controller method in this class.
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param AuthorizationCheckerInterface $authChecker
     * @param UserRepository $userRepository
     * @return Response
     */
    public function manageCandidates(Request $request, AuthorizationCheckerInterface $authChecker, UserRepository $userRepository)
    {
        $verificated = $this->verificateUser($authChecker);


        $em = $this->getDoctrine()->getManager();
        $myJobs = $em->getRepository(Job::class)->findBy([
            'user' => $this->getUser()
        ]);

        $users = $em->getRepository(User::class)->findUsersByJobsAppiled($myJobs);

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
        $path = $this->getParameter('app.path.user_cv') . '/';

        $paymentMetadata = [
            'jobs' => $this->jobService->getCurrentJobPackage($this->getUser()),
            'services' => $this->jobService->getCurrentServicesPackage($this->getUser())
        ];
        return $this->render(
            'user/employer/candidate.html.twig',
            array(
                'verificated_acount' => $verificated,
                'candidates' => $pagination,
                'notifications' => $this->loadNotifications(),
                'url' => $path,
                'paymentMetadata' => $paymentMetadata
            )
        );
    }


    /**
     * @param User $candidate
     * @param Request $request
     * @return JsonResponse|Response
     *
     * @Route ("/send_message/{id}", name="enviar_mensaje_de_correo" )
     */
    public function sendEmail(User $candidate, Request $request)
    {
        $message = new ContactMessage();
        $message->setDestinatario($candidate);

        $form = $this->createForm(ContactMessageFormType::class, $message, [
            'action' => $this->generateUrl('enviar_mensaje_de_correo', ['id' => $candidate->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $message->setDate(new \DateTime('now'));
            $message->setCreator($this->getUser());
            $message->setDestinatario($candidate);

            $em->persist($message);
            $em->flush();

            $this->mailer->sendEmailCandidate($message);

            return new JsonResponse([
                'type' => 'success',
                'message' => 'Mensaje enviado'
            ]);
        }

        return $this->render('user/senMessage.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @param User $candidate
     * @param ContactMessageRepository $messageRepository
     * @return Response
     *
     * @Route ("/find_messages/{id}", name="buscar_mensajes", options={"expose" = true})
     */
    public function getEmails(User $candidate, ContactMessageRepository $messageRepository)
    {
        $employer = $this->getUser();

        return $this->render('user/employer/conversaciones.html.twig', [
            'conversaciones' => $messageRepository->findByCandidate($candidate, $employer),
            'candidato' => $candidate
        ]);
    }

    /**
     * @param Job $job
     * @param Request $request
     * @return Response
     * @Route("/candidates/job/{id}/detail", name="canditate_detail_by_job")
     */
    public function showCandidates(Job $job, Request $request)
    {
        $candidates = [];

        foreach ($job->getUserJobMetadata() as $metadata) {
            if ($metadata->getStatus() == UserJobMeta::STATUS_APPLIED) {
                $candidates[] = $metadata->getUser();
            }
        }

        $pagination = $this->get('knp_paginator')->paginate(
            $candidates,
            $request->query->getInt('page', 1),
            10
        );
        $pagination->setTemplate('site/pagination.html.twig');

        return $this->render('user/employer/show_candidates.html.twig', [
            'candidates' => $pagination,
            'notifications' => $this->loadNotifications(),
            'job' => $job
        ]);
    }

    /**
     * @param User $canditate
     * @param AuthorizationCheckerInterface $authChecker
     * @return Response
     *
     * @Route("/candidate/{id}/detail", name="canditate_detail")
     */
    public function candidateDetails(User $canditate, AuthorizationCheckerInterface $authChecker)
    {
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
     * @param User $destinanario
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/sendContactMessage/{id}", name="sendContactMessage", options={"expose" = true})
     */
    public function sendMessage(User $destinanario, Request $request)
    {
        $user = $this->getUser();

        $message = new ContactMessage();
        $message
            ->setDate(new \DateTime('now'))
            ->setCreator($user)
            ->setDestinatario($destinanario);

        $form = $this->createForm(ContactMessageFormType::class, $message, [
            'action' => $this->generateUrl('sendContactMessage', ['id' => $destinanario->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message
                ->setDate(new \DateTime('now'))
                ->setCreator($user)
                ->setDestinatario($destinanario);

            $em = $this->getDoctrine()->getManager();

            $em->persist($message);
            $em->flush();

            $mailerThemplate = $this->renderView('mail/service_contact.html.twig', [
                'remit' => $message->getCreator(),
                'body' => $message->getContext(),
            ]);

            $this->mailer->sendEmailMessage(
                'Notificación de Benditotrabajo.com',
                $mailerThemplate,
                'benditotrabajoecuador@gmail.com',
                $destinanario->getEmail(),
                'ubelamgelfonseca@gmail.com'
            );

            return new JsonResponse([
                'type' => 'success',
                'message' => 'Mensaje enviado'
            ]);
        }

        return $this->render('user/employer/contactForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/resume/view/{id}", name="resume_view")
     */
    public function resumeView($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        return $this->render(
            'user/resumeView.html.twig',
            array(
                'user' => $user,
                'notifications' => $this->loadNotifications(),
            )
        );
    }

    /**
     * @Route("/dowload/cv/{name}/{username}", name="dowload_cv")
     */
    public function dowloadCv($name, $username)
    {
        try {
            $base = $this->getParameter('kernel.project_dir') . '/public/';
            $path = $this->getParameter('app.path.user_cv') . '/';
            $response = new BinaryFileResponse($base . $path . $name);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $username . '_' . $name);
            return $response;
        } catch (FileNotFoundException $exception) {
            return new Response('File not found');
        }
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
        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        if (empty($currentUser->getSecret())) {
            $currentUser->setSecret(rand(10000, 99999));
        }
        $message = (new \Swift_Message('Código de verificación'))
            ->setFrom('benditotrabajoecuador@gmail.com')
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
        $this->addFlash('success', 'Se ha enviado el código a ' . $currentUser->getEmail());

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("admin/package", name="admin_package")
     */
    public function packageIndex()
    {
        $em = $this->getDoctrine()->getManager();
        $packages = $em->getRepository(Payment::class)->findAll();

        return $this->render(
            'admin/dashboard.html.twig',
            array(
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'packages' => $packages,
            )
        );
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
            $val = $request->request->get('skill-' . $i);
            if ($val != null) {
                $info[] = $val;
            }
        }
        /** @var User $user */
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
        /** @var User $user */
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
     * @return Response
     * @Route("/carousel_company", name="carousel_company", options={"expose" = true})
     */
    public function loadCompanies()
    {
        return $this->render('site/includes/carousel_company.html.twig', [
            'companies' => $this->companyService->findActives()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("/page", name="static_page_view", options={"expose" = true})
     */
    public function showStaticPage(Request $request)
    {
        $type = urldecode($request->query->get('type'));
        /** @var StaticPage $page */
        $page = $this->getDoctrine()->getRepository(StaticPage::class)->findOneBy(['type' => $type]);

        if (null === $page) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('site/static_page.html.twig', [
            'page' => $page,
            'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @Route ("/check_email", name="check_email", options={"expose" = true})
     */
    public function checkEmail(Request $request)
    {
        $email = $request->query->get('email');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        return new JsonResponse([
            'valid' => $user ? false : true
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @throws Exception
     * @Route ("/view_emails", name="emails_view", options={"expose" = true})
     */
    public function loadEmails(Request $request)
    {
        $datatable = $this->datatableFactory->create(emailsDatatable::class);
        $datatable->buildDatatable([
            'url' => $this->generateUrl('emails_view')
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);

            $builder = $this->datatableResponse->getDatatableQueryBuilder();

            $builder->getQb()
                ->where('contactmessage.creator=:user')
                ->setParameter('user', $this->getUser());

            return $this->datatableResponse->getResponse();
        }
        return $this->render('mail/emails_list.html.twig', [
            'datatable' => $datatable
        ]);
    }
}
