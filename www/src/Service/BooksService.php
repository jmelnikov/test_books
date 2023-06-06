<?php


namespace App\Service;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class BooksService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBooksList(int $page, int $per_page): array
    {
        return $this->entityManager->getRepository(Book::class)->getBooksList($page, $per_page);
    }

    public function getBooksListCount(): int
    {
        return $this->entityManager->getRepository(Book::class)->getBooksListCount();
    }

    public function getBookByID(int $id): Book|null
    {
        return $this->entityManager->getRepository(Book::class)->getBookByID($id);
    }

    public function saveBook(int $current_book_id, Request $request): array|bool
    {
        $current_book = $this->getBookByID($current_book_id);

        if(!$current_book instanceof Book) {
            return [
                'result' => 'error',
                'message' => 'not_found'
            ];
        }

        $current_book->setTitle($request->request->get('bookTitle'));
        $current_book->setIsbn($request->request->get('bookIsbn'));
        $current_book->setShortDescription($request->request->get('shortDescription'));
        $current_book->setLongDescription($request->request->get('longDescription'));
        $current_book->setPublishDate(new \DateTime($request->request->get('publishedDate')));

        $this->entityManager->persist($current_book);
        $this->entityManager->flush();

        return true;
    }
}
