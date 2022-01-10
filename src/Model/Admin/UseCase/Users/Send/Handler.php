<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Send;


use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\Id;
use App\Model\User\Entity\User\Notification\Notification;
use App\Model\User\Entity\User\Notification\NotificationRepository;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private $flusher;
    private $notificationRepository;
    private $userRepository;
    private $notificationService;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param NotificationRepository $notificationRepository
     * @param UserRepository $userRepository
     * @param \App\Services\Tasks\Notification $notificationService
     */
    public function __construct(Flusher $flusher, NotificationRepository $notificationRepository, UserRepository $userRepository, \App\Services\Tasks\Notification $notificationService){
        $this->flusher = $flusher;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
    }

    public function handle(Command $command){
        $user = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->id_user));

        $this->flusher->flush();

        $message = Message::sendMessage($user->getEmail(), $command->message);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }

}