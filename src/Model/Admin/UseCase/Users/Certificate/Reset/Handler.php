<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Certificate\Reset;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Id;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\User\UserRepository;
use App\Services\Tasks\Notification;

class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var Notification
     */
    private $notification;

    private $userRepository;

    public function __construct(
        Flusher $flusher,
        ProfileRepository $profileRepository,
        Notification $notification,
        UserRepository $userRepository
    ){
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
        $this->notification = $notification;
        $this->userRepository = $userRepository;
    }

    public function handleApprove(Command $command): void {
        $profile = $this->profileRepository->get(new Id($command->profileId));

        $profile->successConfirmCertificate();
        $user = $profile->getUser();

        $message = Message::certificateSuccessUser($user->getEmail());
        // Клиенту
        $this->notification->sendToOneUser($user, $message);
        $this->notification->emailToOneUser($message);

        // Модеру
        $moderator = $this->userRepository->get(
            new \App\Model\User\Entity\User\Id($command->moderatorId)
        );
        $this->notification->sendToOneUser(
            $moderator,
            Message::certificateSuccessModerator(
                $moderator->getEmail(),
                $profile->getFullName()
            )
        );

        $this->flusher->flush();
    }

    public function handleReject(Command $command): void {
        $profile = $this->profileRepository->get(new Id($command->profileId));

        $profile->failedConfirmCertificate();

        $this->flusher->flush();
    }

}