<?php

namespace App\Controller;

use App\constants;
use App\Datatable\MyJobDatatable;
use App\Entity\Anouncement;
use App\Entity\Image;
use App\Entity\Job;
use App\Entity\Notification;
use App\Entity\Payment;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use App\Form\ServiceJobType;
use App\Repository\AnouncementRepository;
use App\Repository\JobRepository;
use App\Service\JobService;
use App\Service\NotificationService;
use App\Service\UserService;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use function PHPSTORM_META\type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ServiceController
 * @package App\Controller
 */
class ServiceController extends Controller
{

    /** @var UserService */
    private $userService;

    /** @var NotificationService */
    private $notificationService;

    /** @var JobService */
    private $jobService;

    /** @var DatatableFactory */
    private $datatableFactory;

    /** @var DatatableResponse */
    private $datatableResponse;

    /**
     * ServiceController constructor.
     * @param UserService $userService
     * @param NotificationService $notificationService
     * @param JobService $jobService
     * @param $datatableFactory
     * @param $datatableResponse
     */
    public function __construct(UserService $userService, NotificationService $notificationService, JobService $jobService, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->userService = $userService;
        $this->notificationService = $notificationService;
        $this->jobService = $jobService;
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }


    /**
     * @param $data
     * @param Anouncement $post
     * @throws Exception
     */
    public function setMultipleUpload($data, Anouncement $post)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $item) {
            $image = new Image();
            $image->setImageFile($item);
            $image->setUpdateAt(new \DateTime());
            $em->persist($image);
            $post->addImage($image);
        }
        $em->flush();
    }

    /**
     * @param AnouncementRepository $repository
     */
    public function showAplicados(AnouncementRepository $repository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $applied = $user->getApplied();
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/service/new", name="service_new")
     */
    public function serviceNew(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $metadata = $this->userService->isReadyToGetService($user);


        if (!$metadata || !$user->getBuyFreePackService()) {
            $em = $this->getDoctrine()->getManager();
            $packagesServices = $em->getRepository(PaymentForServices::class)->findAll();
            /** @var PaymentForServices $service */
            foreach ($packagesServices as $service) {
                if ($service->getPrice() === 0) {
                    $user->addPackageService($service);
                    $metadata = new PaymentForServicesMetadata();
                    $metadata
                        ->setUser($user)
                        ->setPackage($service)
                        ->setDatePurchase(new \DateTime('now'))
                        ->setActive(true)
                        ->setCurrentPostCount(0);
                    $user->addPaymentForServicesMetadata($metadata);
                    $user->setBuyFreePackService(true);

                    $service->addPaymentForServicesMetadata($metadata);
                    $em->persist($metadata);
                    $em->flush();
                    break;
                }
            }
        }
        /** @var PaymentForServicesMetadata $metadata */
        if (null != $metadata = $this->userService->isReadyToGetService($user)) {

            if ($metadata->getCurrentPostCount() == $metadata->getPackage()->getAnouncementsNumberMax()) {
                $metadata->setActive(false);
                return $this->redirectToRoute('pricing_page', ['type' => 'service']);
            }

            $post = new Anouncement();
            $form = $this->createForm(ServiceJobType::class, $post);
            $entityManager = $this->getDoctrine()->getManager();
            $post->setDate(new \DateTime("now"));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                $payment = $metadata->getPackage();

                $post->setExpiredDate(
                    $post->getDate()->add(\DateInterval::createfromdatestring('+' . $payment->getVisibleDays() . ' day'))
                );
                $entityManager->persist($post);
                foreach ($post->getImages() as $image) {
                    if ($image->getImageFile()) {
                        $image->setUpdateAt(new \DateTime('now'));
                        $image->setManyToOne($post);
                    }

                }
                $post->setDate(new \DateTime("now"));
                $post->setStatus(constants::JOB_STATUS_ACTIVE);

                $metadata->setCurrentPostCount($metadata->getCurrentPostCount() + 1);

                if ($metadata->getCurrentPostCount() === $metadata->getPackage()->getAnouncementsNumberMax())
                    $metadata->setActive(false);

                $post->setUser($user);
                $entityManager->persist($post);
                $entityManager->flush();

                $this->notificationService->create(constants::NOTIFICATION_JOB_CREATE, 'Servicio creado satisfactoriamente', $user);

                return $this->redirectToRoute('service_manage');
            }
            $paymentMetadata = [
                'jobs' => $this->jobService->getCurrentJobPackage($user),
                'services' => $this->jobService->getCurrentServicesPackage($user)
            ];
            return $this->render(
                'service/new.html.twig',
                [
                    'form' => $form->createView(),
                    'notifications' => $this->notificationService->orderByDate($user),
                    'paymentMetadata' => $paymentMetadata
                ]
            );
        }
        return $this->redirectToRoute('pricing_page', ['type' => 'service']);
    }

    /**
     * @param Request $request
     * @param Anouncement $post
     * @return RedirectResponse|Response
     * @throws Exception
     * @Route("/service/{id}/edit", name="service_edit")
     */
    public function serviceEdit(Request $request, Anouncement $post)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ServiceJobType::class, $post);
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($post->getImages() as $image) {
                if ($image->getImageFile()) {
                    $image->setUpdateAt(new \DateTime('now'));
                    $image->setManyToOne($post);
                }

            }
            $entityManager->flush();

            $this->notificationService->create(constants::NOTIFICATION_JOB_UPDATE, 'Servicio modificado satisfactoriamente', $user);

            return $this->redirectToRoute('load_services_request');
        }
        $paymentMetadata = [
            'jobs' => $this->jobService->getCurrentJobPackage($user),
            'services' => $this->jobService->getCurrentServicesPackage($user)
        ];
        return $this->render(
            'service/new.html.twig',
            [
                'form' => $form->createView(),
                'notifications' => $this->notificationService->orderByDate($user),
                'paymentMetadata' => $paymentMetadata
            ]
        );
    }

    /**
     * @Route("/service/manage", name="service_manage")
     * @param Request $request
     * @return Response
     */
    public function manage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $jobs = $em->getRepository(Anouncement::class)->findBy(array('user' => $user), array('date' => 'desc'));
        $pagination = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            5
        );
        $pagination->setTemplate('site/pagination.html.twig');

        return $this->render(
            'service/manage.html.twig',
            [
                'jobs' => $pagination,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'expired' => $this->container->get('app.service.helper')->expired(),
            ]
        );
    }

    /**
     * @Route("/service/view/{id}", name="service_view")
     */
    public function serviceView(Anouncement $anouncement)
    {
        return $this->render(
            'service/view.html.twig',
            array(
                'job' => $anouncement,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
            )
        );
    }

    /**
     * @Route("/service/list", name="service_list")
     * @param Request $request
     * @return Response
     */
    public function serviceList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Anouncement::class)->findBy(array('status' => constants::JOB_STATUS_ACTIVE));
        $pagination = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            5
        );
        $pagination->setTemplate('site/pagination.html.twig');

        return $this->render(
            'service/list.html.twig',
            [
                'jobs' => $pagination,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'professions' => $this->container->get('app.service.helper')->loadProfessions(),
            ]
        );
    }

    /**
     * @Route("/services/search",name="services_search")
     * @param Request $request
     * @param JobRepository $repository
     * @return RedirectResponse|Response
     */
    public function search(Request $request, JobRepository $repository)
    {
        $keywords = $request->request->get('keywords');
        $pagination = $this->get('knp_paginator');
        if (empty($keywords)) {
            return $this->redirectToRoute('service_list');
        } else {
            $pagination->paginate(
                $repository->searchServices($keywords),
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->render(
            'service/list.html.twig',
            array(
                'jobs' => $pagination->paginate(
                    $repository->searchServices($keywords),
                    $request->query->getInt('page', 1),
                    10
                ),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'search' => 1,
            )
        );
    }
}
