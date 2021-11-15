<?php

namespace App\Controller;

use App\Datatable\UserDatatable;
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
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Exception;

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

    private $datatableFactory;

    private $datatableResponse;

    /**
     * BackendController constructor.
     * @param CategoryService $categoryService
     * @param JobService $jobService
     * @param AnnouncementService $announcementService
     */
    public function __construct(
        CategoryService $categoryService,
        JobService $jobService,
        AnnouncementService $announcementService,
        DatatableFactory $datatableFactory,
        DatatableResponse $datatableResponse
    ) {
        $this->categoryService = $categoryService;
        $this->jobService = $jobService;
        $this->announcementService = $announcementService;
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
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

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     * @Route("/backend/userList", name="userList", options={"expose" = true})
     */
    public function userList(Request $request):Response{
        $datatable = $this->datatableFactory->create(UserDatatable::class);
        $datatable->buildDatatable([
            'url' => $this->generateUrl('userList')
        ]);

        if($request->isXmlHttpRequest() && $request->isMethod('POST')){
            $this->datatableResponse->setDatatable($datatable);
            $qb = $this->datatableResponse->getDatatableQueryBuilder();
            return $this->datatableResponse->getResponse();
        }

        return $this->render('backend/user/index.html.twig',[
            'datatable' => $datatable
        ]);
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return Response
     * @Route("/backend/user/remove/{id}", name="userRemove")
     */
    public function removeUser(User $user):Response{
        $em = $this->getDoctrine()->getManager();
        try{
            $em->remove($user);
            $em->flush();
        }
        catch(Exception $e){
            $this->addFlash('error', 'Ha ocurrido un error al eliminar el usuario');
        }
        $this->addFlash('success', 'Usuario eliminado correctamente');
        return new JsonResponse([
            'type' => 'success',
            'messsage' => 'Datos eliminados'
        ]);
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @return Response
     * @Route("/backend/user/activatge/{id}", name="userActivate")
     */
    public function activateDesactivate(User $user):Response{
        $em = $this->getDoctrine()->getManager();
       
        $user->setEnabled(!$user->isEnabled());
        $em->flush();
       
        $this->addFlash('success', 'Usuario modificado correctamente');
        return $this->redirectToRoute('userList');
    }
}