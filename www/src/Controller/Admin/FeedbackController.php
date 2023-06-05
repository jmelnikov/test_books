<?php

namespace App\Controller\Admin;

use App\Entity\Feedback;
use App\Service\Admin\FeedbackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/feedback', name: 'admin.feedback.')]
class FeedbackController extends AbstractController
{
    #[Route('/list/{page}', name: 'list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function list(FeedbackService $feedbackService, int $page): Response
    {
        $feedbacks = $feedbackService->getList($page);

        return $this->render('admin/feedback/list.html.twig', [
            'feedbacks' => $feedbacks
        ]);
    }

    #[Route('/get/{id}', name: 'get', requirements: ['id' => '\d+'], defaults: ['id' => 0])]
    public function get(FeedbackService $feedbackService, int $id): Response
    {
        $feedback = $feedbackService->getByID($id);

        if(!$feedback instanceof Feedback) {
            return $this->redirectToRoute('admin.feedback.list');
        }

        return $this->render('admin/feedback/get.html.twig', [
            'feedback' => $feedback
        ]);
    }
}
