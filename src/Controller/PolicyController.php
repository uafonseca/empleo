<?php

namespace App\Controller;

use App\Entity\Policy;
use App\Form\Policy1Type;
use App\Repository\PolicyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PolicyController
 * @package App\Controller
 * @Route("/backend/policy")
 */
class PolicyController extends AbstractController
{
    /**
     * @param PolicyRepository $policyRepository
     * @return Response
     * @Route("/", name="policy_index", methods={"GET"})
     */
    public function index(PolicyRepository $policyRepository): Response
    {
        return $this->render('backend/policy/index.html.twig', [
            'policies' => $policyRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @param PolicyRepository $repository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @Route("/new", name="policy_new", methods={"GET","POST"})
     */
    public function new(Request $request, PolicyRepository $repository): Response
    {
        $current = $repository->load();
        if ($current !== null)
        {
            /** @var Policy $policy */
            $policy = $current;
            $form = $this->createForm(Policy1Type::class, $policy,[
                'action'=>$this->generateUrl('policy_edit',[
                    'id' => $policy->getId()
                ])
            ]);
        }
        else
        {
            $policy = new Policy();
            $form = $this->createForm(Policy1Type::class, $policy,[
                'action'=>$this->generateUrl('policy_new')
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($policy);
            $entityManager->flush();

            return $this->redirectToRoute('backend');
        }

        return $this->render('backend/policy/new.html.twig', [
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="policy_show", methods={"GET"})
     */
    public function show(Policy $policy): Response
    {
        return $this->render('backend/policy/show.html.twig', [
            'policy' => $policy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="policy_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Policy $policy): Response
    {
        $form = $this->createForm(Policy1Type::class, $policy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend');
        }

        return $this->render('backend/policy/edit.html.twig', [
            'policy' => $policy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="policy_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Policy $policy): Response
    {
        if ($this->isCsrfTokenValid('delete' . $policy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($policy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('policy_index');
    }
}
