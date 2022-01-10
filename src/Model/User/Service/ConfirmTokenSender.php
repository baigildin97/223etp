<?php
declare(strict_types=1);
namespace App\Model\User\Service;


use App\Model\User\Entity\User\Email;
use Twig\Environment;

class ConfirmTokenSender
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, string $token): void{
        $message = (new \Swift_Message('Подтверждение регистрации'))
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/sign-up.html.twig',[
                'token' => $token,
                'username' => $email->getValue()
            ]), 'text/html');

        if (!$this->mailer->send($message)){
            throw new \RuntimeException('Unable to send confirm message.');
        }
    }
}