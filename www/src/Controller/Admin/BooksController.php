<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\AuthorsService;
use App\Service\BooksService;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/books', name: 'admin.books.')]
class BooksController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function list(Request $request, BooksService $booksService, int $page): Response
    {
        $per_page = $request->get('per_page', $_ENV['BOOKS_PER_PAGE']);

        $books = $booksService->getBooksList($page, $per_page);

        return $this->render('admin/books/list.html.twig', [
            'books' => $books,
            'books_count' => $booksService->getBooksListCount(),
            'current_page' => $page,
            'per_page' => $per_page
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(BooksService $booksService, AuthorsService $authorsService, CategoryService $categoryService, int $id): Response
    {
        $book = $booksService->getBookByID($id);

        if(!$book instanceof Book) {
            $this->redirectToRoute('admin.books.list');
        }

        return $this->render('admin/books/get.html.twig', [
            'book' => $book,
            'authors' => $authorsService->getAuthorsInAlphabeticalOrder(),
            'categories' => $categoryService->getCategoriesInAlphabeticalOrder()
        ]);
    }

    #[Route('/save/{id}', name: 'save', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function save(Request $request, BooksService $booksService, int $id): Response
    {
        $booksService->saveBook($id, $request);

        return $this->redirectToRoute('admin.books.get', ['id' => $id]);
    }
}
