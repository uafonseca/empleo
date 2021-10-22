<?php

namespace App\Controller;

use App\Datatable\PaymentForJobDatatable;
use App\Datatable\ReportJobsDatatable;
use App\Entity\PaymentForJobsMetadata;
use App\Utility\DateTime\MonthUtility;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Undocumented class
 * @Route("/report")
 */
class ReportController extends AbstractController
{
    private $datatableFactory;

    private $datatableResponse;

    public function __construct(
        DatatableFactory $datatableFactory,
        DatatableResponse $datatableResponse
    ) {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/", name="report_index")
     */
    public function index(Request $request): Response
    {
        $datatable = $this->datatableFactory->create(
            PaymentForJobDatatable::class
        );

        $datatable->buildDatatable([
            'url' => $this->generateUrl('report_index', $request->query->all()),
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);
            $qb = $this->datatableResponse->getDatatableQueryBuilder();
            if (null != ($id = $request->query->get('id'))) {
                $qb
                    ->getQb()
                    ->where('paymentforjobsmetadata.package = :id')
                    ->setParameter('id', $id);
            }
            try {
                $m = $request->query->get('mont');
                if ($m && $m != '-1') {
                    $qb->getQb()
                        ->where('MONTH(paymentforjobsmetadata.datePurchase) =:m')
                        ->setParameter('m', intval($m) + 1);
                }
            } catch (Exception $e) {
            }

            return $this->datatableResponse->getResponse();
        }
        $pack = null;
        if (null != ($id = $request->query->get('id'))) {
            $pack = $this->getDoctrine()
                ->getRepository(PaymentForJobsMetadata::class)
                ->find($id);
        }
        return $this->render('backend/report/index.html.twig', [
            'datatable' => $datatable,
            'pack' => $pack,
            'months' => MonthUtility::getMonths()
        ]);
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @return Response
     * 
     * @Route("/jobs", name="report_jobs")
     */
    public function reportJobs(Request $request): Response
    {
        $datatable = $this->datatableFactory->create(ReportJobsDatatable::class);

        $datatable->buildDatatable([
            'url' => $this->generateUrl('report_jobs', [
                $request->query->all()
            ])
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);
            $qb = $this->datatableResponse->getDatatableQueryBuilder();
            try {
                $m = $request->query->all()[0]['mont'];
                if ($m && $m != '-1') {
                    $qb->getQb()
                        ->where('MONTH(job.dateCreated) =:m')
                        ->setParameter('m', intval($m) + 1);
                }
            } catch (Exception $e) {
            }


            return $this->datatableResponse->getResponse();
        }

        return $this->render('backend/report/jobs.html.twig', [
            'datatable' => $datatable,
            'months' => MonthUtility::getMonths()
        ]);
    }
}