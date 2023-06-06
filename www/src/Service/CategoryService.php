<?php


namespace App\Service;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function getTopLevelCategories(): array|null
    {
        return $this->entityManager->getRepository(Category::class)->findBy(['parent' => null], ['name' => 'ASC']);
    }

    public function getTopLevelCategoryByID(int $id): Category|null
    {
        return $this->entityManager->getRepository(Category::class)->findOneBy(['parent' => null, 'id' => $id]);
    }

    public function getCategoriesInAlphabeticalOrder(): array
    {
        return $this->entityManager->getRepository(Category::class)->getCategoriesInAlphabeticalOrder();
    }

    public function saveCategory(int $current_category_id, Request $request): array|bool
    {
        $current_category = $this->getCategoryByID($current_category_id);

        if(!$current_category instanceof Category) {
            return [
                'result' => 'error',
                'message' => 'not_found'
            ];
        }

        $parent_category = $this->getTopLevelCategoryByID($request->request->get('parentalCategory'));

        $current_category->setParent($parent_category);
        $current_category->setName($request->request->get('categoryName'));

        $this->entityManager->persist($current_category);
        $this->entityManager->flush();

        return true;
    }
}
