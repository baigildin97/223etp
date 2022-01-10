<?php
declare(strict_types=1);
namespace App\Model\User\Service;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\NewEmailResetToken;
use Twig\Environment;

class NewEmailTokenSender
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Email $email, NewEmailResetToken $token, string $fullName, string $clientIp): void{
        $message = (new \Swift_Message('Подтвердите смену Email адреса'))
            ->setTo($email->getValue())
            ->setBody($this->twig->render('mail/user/mail-reset.html.twig',[
                'token' => $token->getToken(),
                'full_name' => $fullName,
                'client_ip' => $clientIp
            ]), 'text/html');

        if (!$this->mailer->send($message)){
            throw new \RuntimeException('Unable to send confirm message.');
        }
    }
}
