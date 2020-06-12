<?php

namespace App\Controller;

use App\Service\CategoryService;
use App\Service\JobService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
