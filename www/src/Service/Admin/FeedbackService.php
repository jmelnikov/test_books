<?php


namespace App\Service\Admin;


use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;

class FeedbackService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getList(int $page): array
    {
        return $this->entityManager->getRepository(Feedback::class)->getList($page);
    }

    public function getByID(int $id): Feedback|null
    {
        return $this->entityManager->getRepository(Feedback::class)->getByID($id);
    }
}