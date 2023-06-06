<?php

namespace App\Controller;

use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'frontend.')]
class FrontendController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(CategoryService $categoryService): Response
    {
        $categories = $categoryService->getTopLevelCategories();

        return $this->render('frontend/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
