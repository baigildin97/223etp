<?php
declare(strict_types=1);

namespace App\Services\Tasks;


use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Content;
use App\Model\User\Entity\User\User;
use App\Services\Notification\EmailMessageInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\Id;
use App\Model\User\Entity\User\Notification\NotificationRepository;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\ReadModel\User\UserFetcher;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\User\Entity\User\Notification\Notification as NotificationEntity;

class Notification
{
    private $flusher;
    private $notificationRepository;
    private $messageBus;

    public function __construct(Flusher $flusher,
                                NotificationRepository $notificationRepository,
                                MessageBusInterface $messageBus)
    {
        $this->flusher = $flusher;
        $this->notificationRepository = $notificationRepository;
        $this->messageBus = $messageBus;
    }

    public function sendToOneUser(User $user, EmailMessageInterface $message)
    {
        $notification = new NotificationEntity(Id::next(), $message, new \DateTimeImmutable(), $user);
        $this->notificationRepository->add($notification);
        $this->flusher->flush();
    }

    public function sendToMultipleUsers(array $users, EmailMessageInterface $message)
    {
        foreach ($users as $user)
        {
            $notification = new NotificationEntity(Id::next(), $message, new \DateTimeImmutable(), $user);
            $this->notificationRepository->add($notification);
        }

        $this->flusher->flush();
    }

    public function emailToOneUser(EmailMessageInterface $message)
    {
        $this->messageBus->dispatch($message);
    }

    public function emailToMultipleUsers(array $users, EmailMessageInterface $message)
    {
        foreach ($users as $user)
        {
            $message->setMailTo($user->getEmail());
            $this->messageBus->dispatch($message);
        }
    }
}