<?php


namespace App\Service;


use App\Entity\Category;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    public function getCategories(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }
}
