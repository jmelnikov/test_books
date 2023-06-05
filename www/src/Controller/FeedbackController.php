<?php

namespace App\Controller;

use App\Service\FeedbackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/feedback', name: 'feedback.')]
class FeedbackController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(): Response
    {
        return $this->render('feedback/index.html.twig');
    }

    #[Route('/sent', name: 'sent')]
    public function sent(Request $request, FeedbackService $feedbackService): Response
    {
        $validationErrors = $feedbackService->saveFeedback($request);

        if(count($validationErrors) > 0) {
            return $this->render('feedback/index.html.twig', [
                'errors' => $validationErrors
            ]);
        }

        return $this->render('feedback/sent.html.twig', [
            'name' => $request->request->get('name')
        ]);
    }
}
