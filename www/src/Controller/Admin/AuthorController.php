<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Service\AuthorsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/author', name: 'admin.author.')]
class AuthorController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(AuthorsService $authorsService): Response
    {
        $authors = $authorsService->getAuthors();

        return $this->render('admin/author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(AuthorsService $authorsService, int $id): Response
    {
        $author = $authorsService->getAuthorByID($id);

        if(!$author instanceof Author) {
            return $this->redirectToRoute('admin.author.list');
        }

        return $this->render('admin/author/get.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/save/{id}', name: 'save', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function save(Request $request, AuthorsService $authorsService, int $id): Response
    {
        $authorsService->saveAuthor($id, $request);

        return $this->redirectToRoute('admin.author.get', ['id' => $id]);
    }
}
