<?php

namespace App\Controller;

use App\Datatable\CategoryDatatable;
use App\Entity\Category;
use App\Form\Category1Type;
use App\Repository\CategoryRepository;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backend/category")
 */
class CategoryController extends AbstractController
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
     * @param Request $request
     * @return Response
     * @throws \Exception
     * @Route("/", name="category_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $datatable = $this->datatableFactory->create(CategoryDatatable::class);

        $datatable->buildDatatable([
            'url' => $this->generateUrl('category_index')
        ]);

        if ($request->isXmlHttpRequest() && $request->isMethod('POST'))
        {
            $this->datatableResponse->setDatatable($datatable);
            $this->datatableResponse->getDatatableQueryBuilder();

            return $this->datatableResponse->getResponse();
        }

        return $this->render('backend/category/index.html.twig', [
            'categories' => $datatable,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('backend/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Category $category
     * @return Response
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('backend/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return Response
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(Category1Type::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('backend/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return Response
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
