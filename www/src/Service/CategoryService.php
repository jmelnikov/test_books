<?php


namespace App\Service;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCategories(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    public function getCategoryByID(int $id): Category|null
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);
    }
}
