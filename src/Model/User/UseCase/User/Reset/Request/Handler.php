<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Reset\Request;


use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\Service\ResetTokenGenerator;
use App\Model\User\Service\ResetTokenSender;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $users;
    private $flusher;
    private $tokenGenerator;
    private $resetTokenSender;
    private $twig;
    private $notificationService;
    private $router;

    public function __construct(UserRepositoryInterface $userRepository, Flusher $flusher,
                                ResetTokenGenerator $tokenGenerator, ResetTokenSender $resetTokenSender,
                                Environment $twig, NotificationService $notificationService,
                                RouterInterface $router)
    {
        $this->users = $userRepository;
        $this->flusher = $flusher;
        $this->tokenGenerator = $tokenGenerator;
        $this->resetTokenSender = $resetTokenSender;
        $this->twig = $twig;
        $this->notificationService = $notificationService;
        $this->router = $router;
    }

    public function handle(Command $command): void {
        $user = $this->users->getByEmail(new Email($command->email));
        $user->requestPasswordReset($this->tokenGenerator->generate(), new \DateTimeImmutable());
        $this->flusher->flush();

        $confirmURL = $this->router->getContext()->getHost() .
            $this->router->generate('auth.reset.confirm', ['token' => $user->getResetToken()->getToken()]);

        $message = Message::userResetPassword($user->getEmail(), $confirmURL);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }
}