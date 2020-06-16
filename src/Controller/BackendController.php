<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Service\CategoryService;
use App\Service\JobService;
use App\Utility\DateTime\MonthUtility;
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

    private $jobService;


    /**
     * BackendController constructor.
     * @param CategoryService $categoryService
     * @param JobService $jobService
     */
    public function __construct(CategoryService $categoryService, JobService $jobService)
    {
        $this->categoryService = $categoryService;
        $this->jobService = $jobService;
    }


    /**
     * @Route("/backend", name="backend")
     */
    public function index()
    {

        return $this->render('backend/index.html.twig', [
            'categories' => $this->categoryService->findAll(),
            'jobs' => $this->jobService->findAll()
        ]);
    }

    /**
     * @param JobRepository $jobRepository
     * @return JsonResponse
     * @Route("/backend/chart", name="backend_chart", options={"expose" = true})
     */
    public function createChatr(JobRepository $jobRepository){
        $monts = MonthUtility::getMonthsForFormWidget();

        $ouput = [];

        foreach ($monts as $key => $value)
        {
            $ouput['month'][] = $value;
            $ouput['values'][] = $jobRepository->coutByMonth($key)[0];
        }
        return new JsonResponse($ouput);
    }
}
