<?php


namespace App\Service;


use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

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
}
