<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Email\Request;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\NewEmailTokenGenerator;
use App\Model\User\Service\NewEmailTokenSender;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $userRepository;
    private $newEmailTokenGenerator;
    private $flusher;
    private $profileRepository;
    private $notificationService;
    private $router;
    private $settingsFetcher;

    public function __construct(
        UserRepository $userRepository,
        NewEmailTokenGenerator $newEmailTokenGenerator,
        Flusher $flusher,
        ProfileRepository $profileRepository,
        NotificationService $notificationService,
        RouterInterface $router,
        SettingsFetcher $settingsFetcher
    )
    {
        $this->userRepository = $userRepository;
        $this->newEmailTokenGenerator = $newEmailTokenGenerator;
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
        $this->notificationService = $notificationService;
        $this->router = $router;
        $this->settingsFetcher = $settingsFetcher;
    }

    public function handle(Command $command): void {
        $user = $this->userRepository->get(new Id($command->id));
        $profile = $this->profileRepository->get(new \App\Model\User\Entity\Profile\Id($user->getProfile()->getId()->getValue()));
        $newEmail = new Email($command->email);

        if ($this->userRepository->hasByEmail($newEmail)){
            throw new \DomainException('Email is already in use.');
        }

        $user->requestEmailChanging(
            $newEmail,
            $token = $this->newEmailTokenGenerator->generate(),
            new \DateTimeImmutable()
        );

        $confirmURL = $this->router->getContext()->getHost() .
            $this->router->generate('profile.email.confirm', ['token' => $token->getToken()]
        );

        $message = Message::userEmailReset(
            $newEmail,
            $profile->getRepresentative()->getPassport()->getFullName(),
            $command->client_ip,
            $confirmURL
        );

        $this->notificationService->emailToOneUser($message);

        $this->flusher->flush();
    }

}
