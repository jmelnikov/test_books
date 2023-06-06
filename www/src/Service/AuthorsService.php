<?php


namespace App\Service;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;

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
}
