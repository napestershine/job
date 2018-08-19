<?php

namespace Yarsha\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Form\CategoryType;

/**
 * class CategoryController
 * @Route("/admin")
 */
class JobCategoryController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/job/category/new", name="yarsha_admin_category_add")
     */
    public function addCategoryAction(Request $request)
    {
        $categoryService = $this->get('yarsha.service.job_category');
        $form = $this->createForm(CategoryType::class, new Category());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $category = $form->getData();
                $categoryService->saveCategory($category);
                $this->addFlash('success', 'One category added.');

                return $this->redirectToRoute('yarsha_admin_category_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }
        }
        $data['form'] = $form->createView();

        return $this->render("YarshaAdminBundle:JobCategory:add.html.twig", $data);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/job/categories", name="yarsha_admin_category_list")
     */
    public function categoryListAction(Request $request)
    {
        $categoryService = $this->get('yarsha.service.job_category');

        $filters = $request->query->all();

        $data['categories'] = $categoryService->getPaginatedCategoriesList($filters);

        return $this->render("YarshaAdminBundle:JobCategory:list.html.twig", $data);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/job/category/{id}/update", name="yarsha_admin_category_update")
     */
    public function updateAction($id, Request $request)
    {
        $categoryService = $this->get('yarsha.service.job_category');
        $category = $categoryService->getCategoryById($id);
        if (!$category) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $category = $form->getData();
                $categoryService->saveCategory($category);
                $this->addFlash('success', 'Category updated.');

                return $this->redirectToRoute('yarsha_admin_category_list');
            } catch (\Exception $e) {
                $data['errorMessage'] = $e->getMessage();
            }

        }
        $data['category'] = $category;
        $data['form'] = $form->createView();

        return $this->render('YarshaAdminBundle:JobCategory:add.html.twig', $data);
    }

    /**
     * @Route("/job/category/{id}/delete", name="yarsha_admin_category_delete")
     */
    public function deleteAction($id)
    {
        $categoryService = $this->get('yarsha.service.job_category');
        $category = $categoryService->getCategoryById($id);
        if (!$category) {
            return $this->redirectToRoute('yarsha_admin_category_list');
        }
        try {
            $categoryService->deleteCategory($category);
            $this->addFlash('success', 'Category deleted.');

        } catch (\Exception $e) {
            $this->addFlash('warning', 'Unable to delete category.');
        }

        return $this->redirectToRoute('yarsha_admin_category_list');
    }

}
