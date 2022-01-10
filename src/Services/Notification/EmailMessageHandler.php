<?php
declare(strict_types=1);

namespace App\Services\Notification;


use App\Container\Model\User\Service\EmailEnvService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Twig\Environment;
use Symfony\Component\Mime\Email;

class EmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;
    private $twig;
    private $emailEnvService;
    private $logger;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        EmailEnvService $emailEnvService,
        LoggerInterface $logger
    ){
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->emailEnvService = $emailEnvService;
        $this->logger = $logger;
    }

    public function __invoke(EmailMessageInterface $message)
    {
        $content = $this->twig->render('mail.html.twig', [
            'subject' => $message->getSubject(),
            'content' => $message->getContent()
        ]);

        $emailMessage = (new Email())
            ->from($this->emailEnvService->getMailFrom())
            ->to($message->getMailTo()->getValue())
            ->subject($message->getSubject())
            ->html($content);

        //$signer = new SMimeSigner($this->emailEnvService->getMailTlsCert(), $this->emailEnvService->getMailTlsKey());
        
        //$signedMessage = $signer->sign($emailMessage);

        try {
            $this->mailer->send($emailMessage);
        } catch (TransportExceptionInterface $e)
        {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }
}
