<?php

namespace App\Controller;

use App\Datatable\CategoryDatatable;
use App\Datatable\ProfessionsDatatable;
use App\Entity\Profession;
use App\Form\ProfessionType;
use App\Repository\ProfessionRepository;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/profession")
 */
class ProfessionController extends AbstractController
{

    /** @var DatatableFactory  */
    private $datatableFactory;

    /** @var DatatableResponse  */
    private $datatableResponse;

    /**
     * CategoryController constructor.
     * @param DatatableFactory $datatableFactory
     * @param DatatableResponse $datatableResponse
     */
    public function __construct(DatatableFactory $datatableFactory, DatatableResponse$datatableResponse)
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
    }

    /**
     * @Route("/", name="profession_index", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {

        $datatable = $this->datatableFactory->create(ProfessionsDatatable::class);

        $datatable->buildDatatable([
            'url' => $this->generateUrl('profession_index')
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST'))
        {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();

            return $this->datatableResponse->getResponse();
        }

        return $this->render('backend/profession/index.html.twig', [
            'datatable' => $datatable,
        ]);

    }

    /**
     * @Route("/new", name="profession_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $profession = new Profession();
        $form = $this->createForm(ProfessionType::class, $profession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profession);
            $entityManager->flush();

            return $this->redirectToRoute('profession_index');
        }

        return $this->render('backend/profession/new.html.twig', [
            'profession' => $profession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="profession_show", methods={"GET"})
     * @param Profession $profession
     * @return Response
     */
    public function show(Profession $profession): Response
    {
        return $this->render('backend/profession/show.html.twig', [
            'profession' => $profession,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="profession_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Profession $profession
     * @return Response
     */
    public function edit(Request $request, Profession $profession): Response
    {
        $form = $this->createForm(ProfessionType::class, $profession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profession_index');
        }

        return $this->render('backend/profession/edit.html.twig', [
            'profession' => $profession,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="profession_delete")
     * @param Request $request
     * @param Profession $profession
     * @return Response
     */
    public function delete(Request $request, Profession $profession): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($profession);
        $entityManager->flush();

        return new JsonResponse([
            'type' => 'success',
            'message' => 'Datos eliminados'
        ]);
    }
}
