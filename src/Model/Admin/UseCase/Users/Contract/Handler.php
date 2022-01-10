<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Contract;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Services\Tasks\Notification;

class Handler
{
    private $flusher;
    private $userRepository;
    private $notificationService;
    private $settingsFetcher;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param UserRepository $userRepository
     * @param Notification $notificationService
     */
    public function __construct(Flusher $flusher, UserRepository $userRepository, Notification $notificationService, SettingsFetcher $settingsFetcher){
        $this->flusher = $flusher;
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void{
        $user = $this->userRepository->get(new Id($command->id_user));
        $user->getProfile()->signContract(new \DateTimeImmutable($command->period));
        $this->flusher->flush();

        $findNameOrganization = $this->settingsFetcher->findDetailByKey(Key::nameOrganization());
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $message = Message::signContract($user->getEmail(), $findNameOrganization, $findSiteName);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }
}