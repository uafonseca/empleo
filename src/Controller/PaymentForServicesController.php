<?php

namespace App\Controller;

use App\Entity\PaymentForServices;
use App\Form\PaymentForServicesType;
use App\Repository\PaymentForServicesRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backebd/payment/services")
 */
class PaymentForServicesController extends AbstractController
{
    /**
     * @Route("/", name="payment_for_services_index", methods={"GET"})
     * @param PaymentForServicesRepository $paymentForServicesRepository
     * @return Response
     */
    public function index(PaymentForServicesRepository $paymentForServicesRepository): Response
    {
        return $this->render('backend/payment_for_services/index.html.twig', [
            'payment_for_services' => $paymentForServicesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="payment_for_services_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $paymentForService = new PaymentForServices();
        $paymentForService->setUuid(Uuid::uuid4());
        $form = $this->createForm(PaymentForServicesType::class, $paymentForService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentForService);
            $entityManager->flush();

            return $this->redirectToRoute('payment_for_services_index');
        }

        return $this->render('backend/payment_for_services/new.html.twig', [
            'payment_for_service' => $paymentForService,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_for_services_show", methods={"GET"})
     * @param PaymentForServices $paymentForService
     * @return Response
     */
    public function show(PaymentForServices $paymentForService): Response
    {
        return $this->render('backend/payment_for_services/show.html.twig', [
            'payment_for_service' => $paymentForService,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="payment_for_services_edit", methods={"GET","POST"})
     * @param Request $request
     * @param PaymentForServices $paymentForService
     * @return Response
     */
    public function edit(Request $request, PaymentForServices $paymentForService): Response
    {
        $form = $this->createForm(PaymentForServicesType::class, $paymentForService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('payment_for_services_index', [
                'id' => $paymentForService->getId(),
            ]);
        }

        return $this->render('backend/payment_for_services/edit.html.twig', [
            'payment_for_service' => $paymentForService,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_for_services_delete", methods={"DELETE"})
     * @param Request $request
     * @param PaymentForServices $paymentForService
     * @return Response
     */
    public function delete(Request $request, PaymentForServices $paymentForService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentForService->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($paymentForService);
            $entityManager->flush();
        }

        return $this->redirectToRoute('payment_for_services_index');
    }
}
