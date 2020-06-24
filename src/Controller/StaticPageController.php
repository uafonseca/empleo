<?php

namespace App\Controller;

use App\Entity\StaticPage;
use App\Form\StaticPageType;
use App\Repository\StaticPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/static/page")
 */
class StaticPageController extends AbstractController
{
    /**
     * @Route("/", name="static_page_index", methods={"GET"})
     */
    public function index(StaticPageRepository $staticPageRepository): Response
    {
        return $this->render('backend/static_page/index.html.twig', [
            'static_pages' => $staticPageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="static_page_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $staticPage = new StaticPage();
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $staticPage->setUpdateAt(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($staticPage);
            $entityManager->flush();

            return $this->redirectToRoute('static_page_index');
        }

        return $this->render('backend/static_page/new.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_page_show", methods={"GET"})
     */
    public function show(StaticPage $staticPage): Response
    {
        return $this->render('backend/static_page/show.html.twig', [
            'static_page' => $staticPage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="static_page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, StaticPage $staticPage): Response
    {
        $form = $this->createForm(StaticPageType::class, $staticPage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('static_page_index');
        }

        return $this->render('backend/static_page/edit.html.twig', [
            'static_page' => $staticPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="static_page_delete", methods={"DELETE"})
     */
    public function delete(Request $request, StaticPage $staticPage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$staticPage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($staticPage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('static_page_index');
    }
}
