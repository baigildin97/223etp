<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Confirm\ByToken;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Id;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Model\User\Entity\User\Notification\Notification;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $user;
    private $flusher;
    private $settingsFetcher;
    private $notificationService;

    public function __construct(UserRepositoryInterface $user, Flusher $flusher,
                                SettingsFetcher $settingsFetcher, NotificationService $notificationService)
    {
        $this->user = $user;
        $this->flusher = $flusher;
        $this->settingsFetcher = $settingsFetcher;
        $this->notificationService = $notificationService;
    }

    public function handle(Command $command):void {
        if (!$user = $this->user->findByConfirmToken($command->confirmToken)){
            throw new \DomainException("Incorrect or confirmed token.");
        }

        $user->confirmSignUp();
        $this->flusher->flush();

        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $message = new Message($user->getEmail(), $findSiteName);
        $this->notificationService->sendToOneUser($user, $message);
        $this->notificationService->emailToOneUser($message);
    }

}