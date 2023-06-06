<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'books:parse-file',
    description: 'Books parsing command',
)]
class BooksParseFileCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private SymfonyStyle $io;
    private HttpClientInterface $httpClient;
    private KernelInterface $kernel;

    private const DEFAULT_CATEGORY = 'Новинки';

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient, KernelInterface $kernel, string $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->kernel = $kernel;
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::OPTIONAL, 'JSON file name with books list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');

        $this->io->text('Started');

        if(!$this->run_parsing($filename)) {
            $this->io->error('Finished with failure');
            return Command::FAILURE;
        }

        $this->io->success('Parsing completed');
        return Command::SUCCESS;
    }

    private function run_parsing(string $filename): bool
    {
        $books = json_decode(file_get_contents($filename), true);

        if(!is_array($books)) {
            return false;
        }

        $this->io->note(sprintf('Total books: %s', count($books)));

        foreach ($books as $book) {
            $this->addBook($book);
        }

        return true;
    }

    private function addBook($book_data)
    {
        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['title' => $book_data['title'], 'isbn' => $book_data['isbn'] ?? null]);

        if($book instanceof Book) {
            $this->io->info(sprintf('Book named %s already exists', $book->getTitle()));
            return;
        }

        $this->io->info(sprintf('Adding book named %s', $book_data['title']));

        $book = new Book();
        $book->setTitle($book_data['title']);
        $book->setIsbn($book_data['isbn'] ?? null);
        if(!empty($book_data['publishedDate']['$date'])) {
            $book->setPublishDate(new DateTime($book_data['publishedDate']['$date']));
        } else {
            $book->setPublishDate(null);
        }
        $book->setThumbnailUrl($this->downloadImage($book_data['thumbnailUrl'] ?? null));
        $book->setPageCount($book_data['pageCount']);
        $book->setShortDescription($book_data['shortDescription'] ?? null);
        $book->setLongDescription($book_data['longDescription'] ?? null);
        $book->setStatus($book_data['status']);

        foreach ($book_data['authors'] as $author) {
            $book->addAuthor($this->getAuthor($author));
        }

        foreach ($book_data['categories'] as $category) {
            $book->addCategory($this->getCategory($category));
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function getAuthor(string $author_name): Author
    {
        $author = $this->entityManager->getRepository(Author::class)->findOneBy(['name' => $author_name]);

        if(!$author instanceof Author) {
            $author = new Author();
            $author->setName($author_name);

            $this->entityManager->persist($author);
            $this->entityManager->flush();
        }

        return $author;
    }

    private function getCategory(string $category_name): Category
    {
        $category_name = strlen($category_name) > 0 ? $category_name : self::DEFAULT_CATEGORY;

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $category_name]);

        if(!$category instanceof Category) {
            $category = new Category();
            $category->setName($category_name);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        return $category;
    }

    private function downloadImage(?string $thumbnail_url): string|null
    {
        if(!is_string($thumbnail_url)) {
            return null;
        }

        try {
            $imageFile = $this->httpClient->request('GET', $thumbnail_url)->getContent();
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $this->io->warning($e->getMessage());

            return null;
        }

        do {
            $filename = md5(microtime().rand()).'.jpg';
        }
        while (file_exists($filename));

        file_put_contents($this->kernel->getProjectDir().'/public/images/'.$filename, $imageFile);

        return $filename;
    }
}
