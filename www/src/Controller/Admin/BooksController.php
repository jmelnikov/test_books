<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/books', name: 'admin.books.')]
class BooksController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(): Response
    {
        return $this->render('admin/books/list.html.twig', [
            'controller_name' => 'BooksController',
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(): Response
    {
        return $this->render('admin/books/add.html.twig', [
            'controller_name' => 'BooksController',
        ]);
    }
}
