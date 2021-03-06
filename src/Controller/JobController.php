<?php

namespace App\Controller;

use App\AppEvents;
use App\constants;
use App\Datatable\JobDatatable;
use App\Entity\Job;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForJobsMetadata;
use App\Entity\User;
use App\Event\AlertEvent;
use App\Form\JobType;
use App\Mailer\Mailer;
use App\Service\JobService;
use App\Service\NotificationService;
use App\Service\UserService;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JobController extends AbstractController
{


    /** @var DatatableFactory */
    private $datatableFactory;

    /** @var DatatableResponse */
    private $datatableResponse;

    /** @var UserService */
    private $userService;

    /** @var NotificationService */
    private $notificationService;

    /** @var JobService */
    private $jobService;

    /** @var Mailer */
    private $mailer;
    private EventDispatcherInterface $dispatcher;

    /**
     * JobController constructor.
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $datatableResponse
     * @param UserService $userService
     * @param NotificationService $notificationService
     * @param JobService $jobService
     * @param Mailer $mailer
     */
    public function __construct(
        DatatableFactory $datatableFactory,
        DatatableResponse $datatableResponse,
        UserService $userService,
        NotificationService $notificationService,
        JobService $jobService,
        Mailer $mailer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
        $this->userService = $userService;
        $this->notificationService = $notificationService;
        $this->jobService = $jobService;
        $this->mailer = $mailer;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/backend/job", name="job_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $datatable = $this->datatableFactory->create(JobDatatable::class);
        $datatable->buildDatatable([
            'url' => $this->generateUrl('job_index')
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();

            return $this->datatableResponse->getResponse();
        }
        return $this->render('backend/job/index.html.twig', [
            'jobs' => $datatable,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     * @Route("/job/new", name="job_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $metadata = $this->userService->isReadyToGetJob($user);
        if (!$metadata && !$user->getBuyFreePackJob()) {
            $em = $this->getDoctrine()->getManager();
            $paymentForJobs =  $em->getRepository(PaymentForJobs::class)->findAll();
            /** @var PaymentForJobs $paymentForJob */
            foreach ($paymentForJobs as $paymentForJob) {
                if ($paymentForJob->getPrice() === 0) {
                    $user->addPackageJob($paymentForJob);
                    $metadata = new PaymentForJobsMetadata();
                    $metadata
                        ->setUser($user)
                        ->setPackage($paymentForJob)
                        ->setDatePurchase(new \DateTime('now'))
                        ->setActive(true)
                        ->setCurrentPostCount(0);
                    $user->addPaymentForJobsMetadata($metadata);
                    $user->setBuyFreePackJob(true);

                    $paymentForJob->addPaymentForJobsMetadata($metadata);
                    $em->persist($metadata);
                    $em->flush();
                    break;
                }
            }
        }

        /** @var PaymentForJobsMetadata $metadata */
        if (null != $metadata = $this->userService->isReadyToGetJob($user)) {

            if ($metadata->getCurrentPostCount() == $metadata->getPackage()->getAnouncementsNumberMax()) {
                $metadata->setActive(false);
                $this->addFlash('warning', 'Debe acceder a comprar un paquete');
                return $this->redirectToRoute('pricing_page', ['type' => 'job']);
            }

            $post = new Job();
            $post->setUser($user);
            $post->setCompany($user->getCompany());
            $form = $this->createForm(JobType::class, $post);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $payment = $metadata->getPackage();

                $post->setExpiredDate(
                    $post->getDate()->add(\DateInterval::createfromdatestring('+' . $payment->getVisibleDays() . ' day'))
                );

                if ($form->get('date')->getData() > new \DateTime("now")) {
                    $post->setStatus(constants::JOB_STATUS_PENDING);
                } else {
                    $post->setStatus(constants::JOB_STATUS_ACTIVE);
                }

                $metadata->setCurrentPostCount($metadata->getCurrentPostCount() + 1);
                if ($metadata->getCurrentPostCount() === $metadata->getPackage()->getAnouncementsNumberMax())
                    $metadata->setActive(false);

                $post->setDateCreated(new \DateTime("now"));
                $user->setCompany($post->getCompany());

                $this->notificationService->create(constants::NOTIFICATION_JOB_CREATE, 'Empleo creado satisfactoriamente', $user);
                $this->dispatcher->dispatch(new AlertEvent($post), AppEvents::GENERATE_ALERT);

                $this->jobService->update($post);

                return $this->redirectToRoute('job_manage');
            }

            $paymentMetadata = [
                'jobs' => $this->jobService->getCurrentJobPackage($user),
                'services' => $this->jobService->getCurrentServicesPackage($user)
            ];

            return $this->render(
                'site/job/job.html.twig',
                [
                    'form' => $form->createView(),
                    'notifications' => $this->notificationService->orderByDate($user),
                    'company' => $user->getCompany(),
                    'paymentMetadata' => $paymentMetadata
                ]
            );
        }

        return $this->redirectToRoute('pricing_page', ['type' => 'job', 'redirect' => true]);
    }

    /**
     * @Route("/backend/job/{id}", name="job_show_new", methods={"GET"})
     */
    public function show(Job $job): Response
    {
        return $this->render('backend/job/show.html.twig', [
            'job' => $job,
        ]);
    }

    /**
     * @param Request $request
     * @param Job $job
     * @return Response
     * @Route("/{id}/editar", name="job_edit_new", methods={"GET","POST"})
     */
    public function edit(Request $request, Job $job): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('backend/job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Job $job
     * @return Response
     * @Route("/job/delete/{id}", name="job_delete_backend")
     */
    public function delete(Job $job)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $message = 'Trabajo eliminado';
        try {
            $job->setStatus(constants::JOB_STATUS_LOOCK);
            $entityManager->flush();
            $entityManager->remove($job);
            $entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $exception) {
            $message = 'Trabajo bloqueado';

            $user = $this->getUser();

            $mailerThemplate = $this->renderView('mail/job_loock.html.twig', ['user' => $user, 'job' => $job]);

            $this->mailer->sendEmailMessage('Su anuncio no cumple con nuestros t??rminos', $mailerThemplate, ['benditotrabajoecuador@gmail.com'], $job->getUser()->getEmail(), 'benditotrabajoecuador@gmail.com');
        }
        return new JsonResponse([
            'type' => 'success',
            'message' => $message
        ]);
    }
}
