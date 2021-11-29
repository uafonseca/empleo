<?php

/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 2/18/2019
 * Time: 2:38 PM
 */

namespace App\Controller;

use App\AppEvents;
use App\constants;
use App\Datatable\MyJobDatatable;
use App\Entity\Anouncement;
use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Notification;
use App\Entity\Payment;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForServices;
use App\Entity\Profession;
use App\Entity\User;
use App\Event\AlertEvent;
use App\Repository\JobRepository;
use App\Service\JobService;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\JobType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class JobController1 extends Controller
{

    /** @var JobService */
    private $jobservice;

    /** @var DatatableFactory  */
    private $datatableFactory;

    /** @var DatatableResponse  */
    private $datatableResponse;

    private EventDispatcherInterface $dispatcher;

    private UploaderHelper $vich;

    /**
     * JobController1 constructor.
     * @param JobService $jobservice
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $datatableResponse
     */
    public function __construct(JobService $jobservice, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse, EventDispatcherInterface $eventDispatcher, UploaderHelper $vich)
    {
        $this->jobservice = $jobservice;
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
        $this->dispatcher = $eventDispatcher;
        $this->vich = $vich;
    }


    /**
     * @Route("/job/new_/", name="job_new_")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function jobNew(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $post = new Job();
        $post->setCompany($user->getCompany());
        $form = $this->createForm(JobType::class, $post);
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $user->getPackageJobs();
            $post->setExpiredDate(
                $post->getDate()->add(\DateInterval::createfromdatestring('+' . $payment->getVisibleDays() . ' day'))
            );
            if ($form->get('date')->getData() < date("y-m-d", strtotime("today"))) {
                $form->get('date')->addError(new FormError("La fecha de debe ser mayor que la fecha acual"));

                return $this->render(
                    'site/job/job.html.twig',
                    [
                        'form' => $form->createView(),
                        'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                        'expired' => $this->container->get('app.service.helper')->expired(),
                        'company' => $user->getCompany()
                    ]
                );
            } elseif ($form->get('date')->getData() > new \DateTime("now")) {
                $post->setStatus(constants::JOB_STATUS_PENDING);
            } else {
                $post->setStatus(constants::JOB_STATUS_ACTIVE);
            }
            $user->setNumPosts($user->getNumPosts() - 1);
            $entityManager->flush();
            $post->setUser($user);
            $post->setDateCreated(new \DateTime("now"));
            $entityManager->persist($post);
            $entityManager->flush();
            if ($user->getCompany() === null) {
                $user->setCompany($post->getCompany());
            }
            $notification = new Notification();
            $notification->setDate(new \DateTime());
            $notification->setType(constants::NOTIFICATION_JOB_CREATE);
            $notification->setContext("Empleo creado satisfactoriamente");
            $notification->setUser($this->get('security.token_storage')->getToken()->getUser());
            $notification->setActive(true);
            $entityManager->persist($notification);
            $entityManager->flush();
            $this->dispatcher->dispatch(new AlertEvent($post), AppEvents::GENERATE_ALERT);
            return $this->redirectToRoute('job_manage');
        }
        if (null == $this->container->get('app.service.helper')->expired()) {
            return $this->redirectToRoute('pricing_page');
        } elseif (
            $this->container->get('app.service.helper')->expired()['days'] == -1
            || $this->container->get('app.service.helper')->expired()['public'] == 0
        ) {
            return $this->redirectToRoute('pricing_page');
        }
        return $this->render(
            'site/job/job.html.twig',
            [
                'form' => $form->createView(),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'expired' => $this->container->get('app.service.helper')->expired(),
                'company' => $user->getCompany()
            ]
        );
    }

    /**
     * @Route("/search/category/{cat}",name="search_category")
     * @param Request $request
     * @param $cat
     * @return mixed
     */
    public function searchByCategory(Request $request, $cat)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->find($cat);
        $pagination = $this->get('knp_paginator');

        return $this->render(
            'site/job/list.html.twig',
            array(
                'jobs' => $pagination->paginate(
                    $em->getRepository(Job::class)->searchByCategory($category),
                    $request->query->getInt('page', 1),
                    10
                ),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'search' => 1,
                'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
            )
        );
    }

    /**
     * @Route("/search/location/{location}",name="search_location")
     */
    public function searchByLocation(Request $request, $location)
    {
        $em = $this->getDoctrine()->getManager();
        $pagination = $this->get('knp_paginator');

        return $this->render(
            'site/job/list.html.twig',
            array(
                'jobs' => $pagination->paginate(
                    $em->getRepository(Job::class)->searchByLocation($location),
                    $request->query->getInt('page', 1),
                    10
                ),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'search' => 1,
                'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
            )
        );
    }

    /**
     * @Route("/search/city/{city}",name="search_city")
     */
    public function searchByCity(Request $request, $city)
    {
        $em = $this->getDoctrine()->getManager();
        $pagination = $this->get('knp_paginator');

        return $this->render(
            'site/job/list.html.twig',
            array(
                'jobs' => $pagination->paginate(
                    $em->getRepository(Job::class)->searchByCity($city),
                    $request->query->getInt('page', 1),
                    10
                ),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'search' => 1,
                'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Company $company
     * @return Response
     * @Route("/search/company/{id}",name="search_company")
     */
    public function searchByCompany(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $pagination = $this->get('knp_paginator');

        return $this->render(
            'site/job/list.html.twig',
            array(
                'jobs' => $pagination->paginate(
                    $em->getRepository(Job::class)->searchByCompany($company),
                    $request->query->getInt('page', 1),
                    10
                ),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'search' => 1,
                'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
            )
        );
    }

    /**
     * @Route("/search",name="search")
     */
    public function search(Request $request)
    {
        $keywords = $request->request->get('keywords');
        $location = $request->request->get('location');
        $em = $this->getDoctrine()->getManager();
        $pagination = $this->get('knp_paginator');
        if (empty($keywords) && empty($location)) {
            return $this->redirectToRoute('homepage');
        } else {
            return $this->render(
                'site/job/list.html.twig',
                array(
                    'jobs' => $pagination->paginate(
                        $em->getRepository(Job::class)->search($keywords, $location),
                        $request->query->getInt('page', 1),
                        10
                    ),
                    'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                    'search' => 1,
                    'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                    'locations' => $this->container->get('app.service.helper')->loadLocations(),
                )
            );
        }
    }

    /**
     * @Route("/job/list/", name="job_list")
     */
    public function jobList(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $jobs = $em->getRepository(Job::class)->findBy(
            array('status' => constants::JOB_STATUS_ACTIVE),
            array('dateCreated' => 'desc')
        );
        $em->getRepository(Job::class)->expired();
        $pagination = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            10
        );
        $pagination->setTemplate('site/pagination.html.twig');

        return $this->render(
            'site/job/list.html.twig',
            [
                'jobs' => $pagination,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'categorys' => $this->container->get('app.service.helper')->loadCategorys(),
                'locations' => $this->container->get('app.service.helper')->loadLocations(),
            ]
        );
    }

    /**
     * @Route("/job/{id}/", name="job_show")
     */
    public function jobShow($id)
    {
        $em = $this->getDoctrine()->getManager();
        $job = $em->getRepository(Job::class)->find($id);

        $seoPage = $this->container->get('sonata.seo.page');

        $seoPage
            ->addTitleSuffix($job->getTitle())
            ->addMeta('name', 'description', $job->getDescription())
            ->addMeta('property', 'og:title', $job->getTitle())
            ->addMeta('property', 'og:image', $this->vich->asset($job, 'imageFile'))
            ->addMeta('property', 'og:url',  $this->generateUrl('job_show', [
                'id' => $job->getId(),
            ], true))
            ->addMeta('property', 'og:description', $job->getDescription());


        return $this->render(
            'site/job/view.html.twig',
            [
                'job' => $job,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param JobRepository $jobRepository
     * @return Response
     * @Route("/manage/job/", name="job_manage")
     * @IsGranted("ROLE_ADMIN")
     */
    public function manage(Request $request, JobRepository $jobRepository)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $jobs = $jobRepository->finJobByUser($user);
        $pagination = $this->get('knp_paginator')->paginate(
            $jobs,
            $request->query->getInt('page', 1),
            5
        );
        $pagination->setTemplate('site/pagination.html.twig');
        $paymentMetadata = ['jobs' => $this->jobservice->getCurrentJobPackage($user)];
        return $this->render(
            'user/employer/manage_job.html.twig',
            [
                'jobs' => $pagination,
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'expired' => $this->container->get('app.service.helper')->expired(),
                'paymentMetadata' => $paymentMetadata
            ]
        );
    }

    /**
     * @Route("/ajax/job/remove", name="ajax_job_remove")
     * @param Request $request
     * @return JsonResponse
     */
    public function removeJob(Request $request)
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
     * @param Request $request
     * @return JsonResponse
     */
    public function searchWithFilters(Request $request)
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
            case "Últimas 24 horas":
                $time = 24;
                break;
            case "Últimos 7 días":
                $time = 24 * 7;
                break;
            case "Últimos 14 días":
                $time = 24 * 14;
                break;
            case "Últimos 30 días":
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
        $newTime = $t->add(\DateInterval::createFromDateString('-' . $time . ' hours'));
        if ($time > 0) {
            $i = 0;
            /** @var Job $job */
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
    public function searchWithFiltersServices(Request $request)
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
            case "Últimas 24 horas":
                $time = 24;
                break;
            case "Últimos 7 días":
                $time = 24 * 7;
                break;
            case "Últimos 14 días":
                $time = 24 * 14;
                break;
            case "Últimos 30 días":
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
        $newTime = $t->add(\DateInterval::createFromDateString('-' . $time . ' hours'));
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
     * @param Request $request
     * @return JsonResponse|Response
     * @throws Exception
     *
     * @Route ("/misServicios", name="my_services_list")
     */
    public function misServiciosSolicitados(Request $request)
    {
        $datatable = $this->datatableFactory->create(MyJobDatatable::class);

        $datatable->buildDatatable([
            'url' => $this->generateUrl('my_services_list')
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);
            $builder = $this->datatableResponse->getDatatableQueryBuilder();

            $builder->getQb()
                ->where('job.user=:user')
                ->setParameter('user', $this->getUser());

            return $this->datatableResponse->getResponse();
        }
        return $this->render('site/job/myServices.html.twig', [
            'datatable' => $datatable,
            'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
        ]);
    }





    /**
     * @param Request $request
     * @param Job $post
     * @return RedirectResponse|Response
     *
     * @Route ("/actualizar/{id}", name="actualizar_trabajo")
     */
    public function editJob(Request $request, Job $post)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(JobType::class, $post);
        $entityManager = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $notification = new Notification();
            $notification->setDate(new \DateTime());
            $notification->setType(constants::NOTIFICATION_JOB_UPDATE);
            $notification->setContext("Empleo modificado satisfactoriamente");
            $notification->setUser($this->get('security.token_storage')->getToken()->getUser());
            $notification->setActive(true);
            $entityManager->persist($notification);
            $entityManager->flush();
            return $this->redirectToRoute('my_services_list');
        }
        return $this->render(
            'site/job/job.html.twig',
            [
                'form' => $form->createView(),
                'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
                'expired' => $this->container->get('app.service.helper')->expired(),
                'company' => $user->getCompany()
            ]
        );
    }
}
