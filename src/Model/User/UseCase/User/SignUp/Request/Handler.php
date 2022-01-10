<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;

use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\Service\PasswordHashGenerator;
use App\Model\User\Service\TokenGenerator;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Model\User\Entity\User\Notification\Notification;
use App\Model\User\Entity\User\Notification\Id;
use App\Model\User\Entity\User\Id as UserId;
use App\Services\Tasks\Notification as NotificationService;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class Handler
{
    private $userRepository;
    private $hashGenerator;
    private $tokenGenerator;
    private $confirmTokenSender;
    private $notificationService;
    private $flusher;
    private $settingsFetcher;
    private $router;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordHashGenerator $hashGenerator,
        TokenGenerator $tokenGenerator,
        NotificationService $notificationService,
        SettingsFetcher $settingsFetcher,
        Flusher $flusher,
        RouterInterface $router
)
    {
        $this->userRepository = $userRepository;
        $this->hashGenerator = $hashGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->flusher = $flusher;
        $this->router = $router;
    }


    public function handle(Command $command):void {
        $email = new Email($command->email);

        if ($this->userRepository->hasByEmail($email)){
            throw new \DomainException("User already exists.");
        }

        $user = User::signUpByEmail(
            UserId::next(),
            new \DateTimeImmutable(),
            $email,
            $this->hashGenerator->hash($command->plainPassword),
            $token = $this->tokenGenerator->generate(),
            $command->clientIp
        );

        $this->userRepository->add($user);

        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
      
        $urlConfirm = $this->getBaseUrl().$this->router->generate('auth.sign.confirm', ['token' => $token]);

        $message = Message::confirmSignUp($user->getEmail(), $findSiteName, $urlConfirm);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }


    private function getBaseUrl(){
        return $this->router->getContext()->getScheme().'://'.$this->router->getContext()->getHost();
    }
}