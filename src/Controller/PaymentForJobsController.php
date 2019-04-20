<?php

namespace App\Controller;

use App\Entity\PaymentForJobs;
use App\Form\PaymentForJobsType;
use App\Repository\PaymentForJobsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment/for/jobs")
 */
class PaymentForJobsController extends Controller
{
    /**
     * @Route("/", name="payment_for_jobs_index", methods={"GET"})
     */
    public function index(PaymentForJobsRepository $paymentForJobsRepository): Response
    {
        return $this->render('payment_for_jobs/index.html.twig', [
            'payment_for_jobs' => $paymentForJobsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="payment_for_jobs_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $paymentForJob = new PaymentForJobs();
        $form = $this->createForm(PaymentForJobsType::class, $paymentForJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentForJob);
            $entityManager->flush();

            return $this->redirectToRoute('payment_index');
        }

        return $this->render('payment_for_jobs/new.html.twig', [
            'payment_for_job' => $paymentForJob,
            'form' => $form->createView(),
	        'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_for_jobs_show", methods={"GET"})
     */
    public function show(PaymentForJobs $paymentForJob): Response
    {
        return $this->render('payment_for_jobs/show.html.twig', [
            'payment_for_job' => $paymentForJob,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="payment_for_jobs_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PaymentForJobs $paymentForJob): Response
    {
        $form = $this->createForm(PaymentForJobsType::class, $paymentForJob);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('payment_for_jobs_index', [
                'id' => $paymentForJob->getId(),
            ]);
        }

        return $this->render('payment_for_jobs/edit.html.twig', [
            'payment_for_job' => $paymentForJob,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_for_jobs_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PaymentForJobs $paymentForJob): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentForJob->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($paymentForJob);
            $entityManager->flush();
        }

        return $this->redirectToRoute('payment_for_jobs_index');
    }
}
