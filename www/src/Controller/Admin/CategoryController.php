<?php

namespace App\Controller\Admin;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category', name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(CategoryService $categoryService): Response
    {
        $categories = $categoryService->getCategories();

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(CategoryService $categoryService, int $id): Response
    {
        $category = $categoryService->getCategoryByID($id);
        $categories = $categoryService->getTopLevelCategories();

        return $this->render('admin/category/get.html.twig', [
            'category' => $category,
            'categories' => $categories
        ]);
    }

    #[Route('/save/{id}', name: 'save', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function save(Request $request, CategoryService $categoryService, int $id): Response
    {
        $categoryService->saveCategory($id, $request);

        return $this->redirectToRoute('admin.category.get', ['id' => $id]);
    }
}
