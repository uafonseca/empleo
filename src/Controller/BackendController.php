<?php

namespace App\Controller;

use App\Entity\PaymentForJobs;
use App\Repository\AnouncementRepository;
use App\Repository\JobRepository;
use App\Service\AnnouncementService;
use App\Service\CategoryService;
use App\Service\JobService;
use App\Utility\DateTime\MonthUtility;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BackendController
 *
 * @package App\Controller
 */
class BackendController extends AbstractController
{
    /** @var CategoryService  */
    private $categoryService;

    /** @var JobService  */
    private $jobService;

    /** @var AnnouncementService  */
    private $announcementService;

    /**
     * BackendController constructor.
     * @param CategoryService $categoryService
     * @param JobService $jobService
     * @param AnnouncementService $announcementService
     */
    public function __construct(
        CategoryService $categoryService,
        JobService $jobService,
        AnnouncementService $announcementService
    ) {
        $this->categoryService = $categoryService;
        $this->jobService = $jobService;
        $this->announcementService = $announcementService;
    }

    /**
     * @Route("/backend", name="backend")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $payments = $em
            ->getRepository(PaymentForJobs::class)
            ->getThisMonth(date('m'));

        return $this->render('backend/index.html.twig', [
            'categories' => $this->categoryService->findAll(),
            'jobs' => $this->jobService->findAll(),
            'announcements' => $this->announcementService->findAll(),
            'payments' => $payments,
        ]);
    }

    /**
     * @param JobRepository $jobRepository
     * @param AnouncementRepository $anouncementRepository
     * @return JsonResponse
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @Route("/backend/chart", name="backend_chart", options={"expose" = true})
     */
    public function createChatr(
        JobRepository $jobRepository,
        AnouncementRepository $anouncementRepository
    ) {
        $months = MonthUtility::getMonthsForFormWidget();

        $outputs = [];

        foreach ($months as $key => $value) {
            $outputs['month'][] = $value;
            $outputs['jobs'][] = $jobRepository->countByMonth($key)[0];
            $outputs['services'][] = $anouncementRepository->countByMonth(
                $key
            )[0];
        }
        return new JsonResponse($outputs);
    }
}