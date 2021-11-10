<?php

namespace App\Controller;

use App\Entity\Consulta;
use App\Entity\RespuestaConsulta;
use App\Form\RespuestaConsultaType;
use App\Repository\RespuestaConsultaRepository;
use App\Repository\ResumeRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/respuesta/consulta")
 */
class RespuestaConsultaController extends AbstractController
{
    /**
     * @Route("/", name="respuesta_consulta_index", methods={"GET"})
     */
    public function index(RespuestaConsultaRepository $respuestaConsultaRepository): Response
    {
        return $this->render('respuesta_consulta/index.html.twig', [
            'respuesta_consultas' => $respuestaConsultaRepository->getMyRespuestas($this->getUser()),
        ]);
    }

    /**
     * @Route("/new", name="respuesta_consulta_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $respuestaConsultum = new RespuestaConsulta();
        $form = $this->createForm(RespuestaConsultaType::class, $respuestaConsultum,[
            'action' => $this->generateUrl('respuesta_consulta_new',[
                'id' => $request->query->get('id')
            ])
        ]);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        $consulta = null;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Consulta $consulta */
            $consulta = $entityManager->getRepository(Consulta::class)->find($request->query->get('id'));
            $consulta->addRespuesta($respuestaConsultum);
            $respuestaConsultum->setConsulta($consulta);
            $respuestaConsultum->setCreatedAt(new DateTime('now'));
            $entityManager->persist($respuestaConsultum);
            $entityManager->flush();

            return new JsonResponse([
                'type' => 'success',
                'message' => 'Datos enviados'
            ]);
        }

        
        $consulta = $entityManager->getRepository(Consulta::class)->find($request->query->get('id'));
        return $this->render('respuesta_consulta/new.html.twig', [
            'respuesta_consultum' => $respuestaConsultum,
            'form' => $form->createView(),
            'consulta' => $consulta
        ]);
    }

    /**
     * @Route("/{id}", name="respuesta_consulta_show", methods={"GET"})
     */
    public function show(RespuestaConsulta $respuestaConsultum): Response
    {
        return $this->render('respuesta_consulta/show.html.twig', [
            'respuesta_consultum' => $respuestaConsultum,
        ]);
    }


     /**
     * @Route("/mostrar/{id}", name="respuesta_consulta_mostrar", methods={"GET"})
     */
    public function mostrar(Consulta $consulta): Response
    {
        return $this->render('respuesta_consulta/show.html.twig', [
            'respuesta_consultum' => $consulta->getRespuestas()->count() ? $consulta->getRespuestas()[0] : null,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="respuesta_consulta_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RespuestaConsulta $respuestaConsultum): Response
    {
        $form = $this->createForm(RespuestaConsultaType::class, $respuestaConsultum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('respuesta_consulta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('respuesta_consulta/edit.html.twig', [
            'respuesta_consultum' => $respuestaConsultum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="respuesta_consulta_delete", methods={"POST"})
     */
    public function delete(Request $request, RespuestaConsulta $respuestaConsultum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$respuestaConsultum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($respuestaConsultum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('respuesta_consulta_index', [], Response::HTTP_SEE_OTHER);
    }
}
