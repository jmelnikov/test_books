<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category.')]
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(CategoryService $categoryService): Response
    {
        $categories = $categoryService->getCategories();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(CategoryService $categoryService, int $id): Response
    {
        $category = $categoryService->getCategoryByID($id);

        if(!$category instanceof Category) {
            return $this->redirectToRoute('category.list');
        }

        return $this->render('category/get.html.twig', [
            'category' => $category,
        ]);
    }
}
