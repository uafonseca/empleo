<?php

namespace App\Controller;

use App\Entity\Title;
use App\Form\TitleType;
use App\Repository\TitleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/title")
 */
class TitleController extends AbstractController
{
    /**
     * @Route("/", name="title_index", methods={"GET"})
     */
    public function index(TitleRepository $titleRepository): Response
    {
        return $this->render('backend/title/index.html.twig', [
            'titles' => $titleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="title_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $title = new Title();
        $form = $this->createForm(TitleType::class, $title);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($title);
            $entityManager->flush();

            return $this->redirectToRoute('title_index');
        }

        return $this->render('backend/title/new.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="title_show", methods={"GET"})
     */
    public function show(Title $title): Response
    {
        return $this->render('backend/title/show.html.twig', [
            'title' => $title,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="title_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Title $title): Response
    {
        $form = $this->createForm(TitleType::class, $title);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('title_index');
        }

        return $this->render('backend/title/edit.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="title_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Title $title): Response
    {
        if ($this->isCsrfTokenValid('delete'.$title->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($title);
            $entityManager->flush();
        }

        return $this->redirectToRoute('title_index');
    }
}