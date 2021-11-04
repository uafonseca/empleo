<?php

namespace App\Controller;

use App\Entity\Alert;
use App\Form\AlertType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/alert")
 */
class AlertController extends AbstractController
{
    /**
     * @Route("/", name="alert_index", methods={"GET"})
     */
    public function index(): Response
    {
        $alerts = $this->getDoctrine()
            ->getRepository(Alert::class)
            ->findAll();

        return $this->render('alert/index.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * @Route("/new", name="alert_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $alert = new Alert();
  
        if ($request->isXmlHttpRequest()) {

            $status = $this->checkEmail($request->query->get('email'));
            if($status['type'] === 'success'){
                $entityManager = $this->getDoctrine()->getManager();
                $alert->setEmail($request->query->get('email'));
                $entityManager->persist($alert);
                $entityManager->flush();
            }
            return new JsonResponse($status);
        }
    }


    /**
     * Undocumented function
     *
     * @param Request $request
     * @return array
     *
     */
    public function checkEmail($email):array
    {
        $em = $this->getDoctrine()->getManager();

        $alerts = $em->getRepository(Alert::class)->findBy(['email' => $email]);
        if(count($alerts) === 0){
            $users = $em->getRepository(\App\Entity\User::class)->findBy(['email' => $email]);
            if(count($users) === 0)
                return [
                    'type' => 'success',
                    'message' => 'Se ha registrado una alerta'
                ];
                else{
                    return [
                        'type' => 'warning',
                        'message' => 'Ya existe un usuario con el email '.$email
                    ]; 
                }
        }else{
            return [
                'type' => 'warning',
                'message' => 'Ya existe una alerta con el email '.$email
            ]; 
        }
    }

    /**
     * @Route("/{id}", name="alert_show", methods={"GET"})
     */
    public function show(Alert $alert): Response
    {
        return $this->render('alert/show.html.twig', [
            'alert' => $alert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="alert_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Alert $alert): Response
    {
        $form = $this->createForm(AlertType::class, $alert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('alert_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alert/edit.html.twig', [
            'alert' => $alert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="alert_delete", methods={"POST"})
     */
    public function delete(Request $request, Alert $alert): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alert->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($alert);
            $entityManager->flush();
        }

        return $this->redirectToRoute('alert_index', [], Response::HTTP_SEE_OTHER);
    }
}
