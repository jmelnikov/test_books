<?php


namespace App\Service;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAuthors(): array
    {
        return $this->entityManager->getRepository(Author::class)->findAll();
    }

    public function getAuthorByID(int $id): Author|null
    {
        return $this->entityManager->getRepository(Author::class)->findOneBy(['id' => $id]);
    }

    public function getAuthorsInAlphabeticalOrder(): array
    {
        return $this->entityManager->getRepository(Author::class)->getAuthorsInAlphabeticalOrder();
    }

    public function saveAuthor(int $current_author_id, Request $request): array|bool
    {
        $current_author = $this->getAuthorByID($current_author_id);

        if(!$current_author instanceof Author) {
            return [
                'result' => 'error',
                'message' => 'not_found'
            ];
        }

        $current_author->setName($request->request->get('authorName'));

        $this->entityManager->persist($current_author);
        $this->entityManager->flush();

        return true;
    }
}
