<?php

namespace App\Controller;

use App\Datatable\ConsultaDatatable;
use App\Entity\Consulta;
use App\Form\ConsultaType;
use App\Entity\Notification;
use App\Entity\Slide;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;

use function GuzzleHttp\Promise\queue;

/**
 * @Route("/consulta")
 */
class ConsultaController extends AbstractController
{

    private $datatableFactory;
    private $datatableResponse;

    public function __construct(DatatableFactory $datatableFactory, DatatableResponse $datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    /**
     * @Route("/", name="consulta_index")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slides = $entityManager->getRepository(Slide::class)->findAll();

        $datatable = $this->datatableFactory->create(ConsultaDatatable::class);
        $datatable->buildDatatable([
            'url' => $this->generateUrl('consulta_index', $request->query->all()),
            'props' => $request->query->all()
        ]);

        $template = 'backend/consultas/index.html.twig';

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $this->datatableResponse->setDatatable($datatable);
            $qb = $this->datatableResponse->getDatatableQueryBuilder();
            if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                $qb
                    ->getQb()
                    ->where('consulta.user = :user')
                    ->setParameter('user', $this->getUser());
                $template = 'consulta/index.html.twig';
            }

            $type = $request->query->get('type');
            if ($type && $type === 'user') {
                $qb
                    ->getQb()
                    ->join('consulta.user', 'usuario')
                    ->where('usuario.candidate =:t')
                    ->setParameter('t', true);
            } elseif ($type && $type === 'company') {
                $qb
                    ->getQb()
                    ->join('consulta.user', 'usuario')
                    ->where('usuario.candidate =:t')
                    ->setParameter('t', false);
            }


            return $this->datatableResponse->getResponse();
        }

        return $this->render($template, [
            'slides' => $slides,
            'notifications' => $this->loadNotifications(),
            'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/new", name="consulta_new", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new(Request $request): Response
    {
        $consultum = new Consulta();
        $form = $this->createForm(ConsultaType::class, $consultum);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $slides = $entityManager->getRepository(Slide::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $loggedUser */
            $loggedUser = $this->getUser();
            $consultum->setUser($loggedUser);
            $loggedUser->addConsulta($consultum);
            $consultum->setCreatedAt(new DateTime('now'));

            $entityManager->persist($consultum);
            $entityManager->flush();

            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consulta/new.html.twig', [
            'consultum' => $consultum,
            'form' => $form->createView(),
            'slides' => $slides,
            'notifications' => $this->loadNotifications(),
        ]);
    }

    /**
     * @return object[]|null
     */
    public function loadNotifications()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if (null != $user) {
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository(Notification::class)->findBy(
                array(
                    'user' => $user,
                    'active' => true,
                ),
                array(
                    'date' => 'DESC',
                ),
                10
            );

            return $notifications;
        }
        return null;
    }

    /**
     * @Route("/{id}", name="consulta_show", methods={"GET"})
     */
    public function show(Consulta $consultum): Response
    {
        return $this->render('consulta/show.html.twig', [
            'consultum' => $consultum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="consulta_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Consulta $consultum): Response
    {
        $form = $this->createForm(ConsultaType::class, $consultum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('consulta_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consulta/edit.html.twig', [
            'consultum' => $consultum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="consulta_delete", methods={"POST"})
     */
    public function delete(Request $request, Consulta $consultum): Response
    {
        if ($this->isCsrfTokenValid('delete' . $consultum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($consultum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('consulta_index', [], Response::HTTP_SEE_OTHER);
    }
}