<?php

namespace App\Controller;

use App\Entity\Author;
use App\Service\AuthorsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author', name: 'author.')]
class AuthorController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(AuthorsService $authorsService): Response
    {
        $authors = $authorsService->getAuthors();

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(AuthorsService $authorsService, int $id): Response
    {
        $author = $authorsService->getAuthorByID($id);

        if(!$author instanceof Author) {
            return $this->redirectToRoute('author.list');
        }

        return $this->render('author/get.html.twig', [
            'author' => $author,
        ]);
    }
}
