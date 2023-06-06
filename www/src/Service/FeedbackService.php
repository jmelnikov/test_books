<?php


namespace App\Service;


use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeedbackService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->mailer = $mailer;
    }

    public function saveFeedback(Request $request): array
    {
        $errors = [];
        $feedback = new Feedback();

        $feedback->setEmail($request->request->get('email'));
        $feedback->setname($request->request->get('name'));
        $feedback->setPhone($request->request->get('phone'));
        $feedback->setMessage($request->request->get('message'));

        if($this->validator->validateProperty($feedback, 'email')->count() > 0) {
            $errors[] = [
                'type' => 'email',
                'message' => 'Некорректный EMail адрес'
            ];
        }

        if($this->validator->validateProperty($feedback, 'message')->count() > 0) {
            $errors[] = [
                'type' => 'message',
                'message' => 'Сликом короткий текст сообщения'
            ];
        }

        if(count($errors) === 0) {
            $this->sendMail($feedback);

            $this->entityManager->persist($feedback);
            $this->entityManager->flush();
        }

        return $errors;
    }

    private function sendMail(Feedback $feedback)
    {
        $mail = new Email();
        $mail->from($_ENV['SENDER_EMAIL']);
        $mail->to($_ENV['SERVICE_EMAIL']);
        $mail->subject('Новое сообщение обратной связи');
        $mail->text($feedback->getMessage());
    }
}
