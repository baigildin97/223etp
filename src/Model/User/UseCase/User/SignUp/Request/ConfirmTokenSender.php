<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;


use App\Model\User\Entity\User\Email;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;

class ConfirmTokenSender
{
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function send(Email $email, string $token): void{
        $this->messageBus->dispatch(new ConfirmNotification());
    }
}