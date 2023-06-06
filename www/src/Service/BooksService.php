<?php


namespace App\Service;


use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function searchBook(Request $request): array|null
    {
        return $this->entityManager->getRepository(Book::class)->searchByQuery($request);
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

        /**
         * @var UploadedFile $thumbnail
         */
        $thumbnail = $request->files->get('thumbnailFile');

        if(!empty($current_book->getThumbnailUrl())) {
            $filename = $current_book->getThumbnailUrl();
        } else {
            do {
                $filename = md5(microtime().rand()).'.jpg';
            }
            while (file_exists(dirname(__DIR__, 2).'/public/images/'.$filename));
        }

        file_put_contents($filename, $thumbnail->getContent());

        $current_book->setTitle($request->request->get('bookTitle'));
        $current_book->setIsbn($request->request->get('bookIsbn'));
        $current_book->setPageCount($request->request->get('pageCount'));
        $current_book->setShortDescription($request->request->get('shortDescription'));
        $current_book->setLongDescription($request->request->get('longDescription'));
        $current_book->setPublishDate(new \DateTime($request->request->get('publishedDate')));

        foreach ($current_book->getAuthor() as $author) {
            $current_book->removeAuthor($author);
        }

        foreach ($request->request->all('bookAuthor') as $author_id) {
            $author = $this->entityManager->getRepository(Author::class)->find($author_id);

            if($author instanceof Author) {
                $current_book->addAuthor($author);
            }
        }

        foreach ($current_book->getCategory() as $category) {
            $current_book->removeCategory($category);
        }

        foreach ($request->request->all('bookCategory') as $category_id) {
            $category = $this->entityManager->getRepository(Category::class)->find($category_id);

            if($category instanceof Category) {
                $current_book->addCategory($category);
            }
        }

        $this->entityManager->persist($current_book);
        $this->entityManager->flush();

        return true;
    }
}
