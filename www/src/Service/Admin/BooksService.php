<?php


namespace App\Service\Admin;


use App\Entity\Book;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
