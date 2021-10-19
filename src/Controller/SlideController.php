<?php

namespace App\Controller;

use App\Entity\Slide;
use App\Form\Slide1Type;
use App\Form\SlideType;
use App\Repository\SlideRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/slide")
 */
class SlideController extends AbstractController
{
    /**
     * @Route("/", name="slide_index", methods={"GET"})
     */
    public function index(SlideRepository $slideRepository): Response
    {
        return $this->render('slide/index.html.twig', [
            'slides' => $slideRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="slide_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $slide = new Slide();
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($slide);
            $entityManager->flush();

            return $this->redirectToRoute('slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('slide/new.html.twig', [
            'slide' => $slide,
            'form' => $form->createView(),
        ]);
    }

  

    /**
     * @Route("/{id}/edit", name="slide_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Slide $slide): Response
    {
        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('slide_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('slide/edit.html.twig', [
            'slide' => $slide,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="slide_delete")
     */
    public function delete(Request $request, Slide $slide): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($slide);
        $entityManager->flush();


        return $this->redirectToRoute('slide_index', [], Response::HTTP_SEE_OTHER);
    }

    
    /**
     * Activa o desactiva un banner dependiendo de su estado inicial
     *
     * @Route("/activate/{id}", name="slide_activate")
     *
     * @param Slide $slide
     * @return Response
     */
    public function activate(Slide $slide):Response
    {
        $em = $this->getDoctrine()->getManager();
        $slide->setActive(!$slide->getActive());
        $em->flush();

        return $this->redirectToRoute('slide_index');
    }
}