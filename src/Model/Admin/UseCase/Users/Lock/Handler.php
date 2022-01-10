<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Lock;


use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $flusher;

    private $userRepository;

    private $notificationService;

    public function __construct(Flusher $flusher, UserRepository $userRepository, NotificationService $notificationService){
        $this->flusher = $flusher;
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
    }

    public function handleLocked(Command $command): void{
        $user = $this->userRepository->get(new Id($command->id_user));
        $user->locked();
        $this->flusher->flush();

        $message = Message::notifyAccountLocked($user->getEmail(), $command->cause);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }

    public function handleUnlocked(Command $command): void{
        $user = $this->userRepository->get(new Id($command->id_user));
        $user->unlocked();
        $this->flusher->flush();

        $message = Message::notifyAccountUnlocked($user->getEmail(), $command->cause);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }


}