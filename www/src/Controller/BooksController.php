<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\BooksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/books', name: 'books.')]
class BooksController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function list(Request $request, BooksService $booksService, int $page): Response
    {
        $per_page = $request->get('per_page', BookRepository::DEFAULT_PER_PAGE);

        $books = $booksService->getBooksList($page, $per_page);

        return $this->render('books/list.html.twig', [
            'books' => $books,
            'books_count' => $booksService->getBooksListCount(),
            'current_page' => $page,
            'per_page' => $per_page
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(BooksService $booksService, int $id): Response
    {
        $book = $booksService->getBookByID($id);

        if(!$book instanceof Book) {
            $this->redirectToRoute('books.list');
        }

        return $this->render('books/get.html.twig', [
            'book' => $book
        ]);
    }
}
