<?php


namespace App\Model\User\UseCase\Certificate\Reset\Confirm;


use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\UseCase\Certificate\Reset\Message;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $certificateRepository;
    private $userRepository;
    private $flusher;
    private $notificationService;

    public function __construct(CertificateRepository $certificateRepository,
                                UserRepository $userRepository,
                                Flusher $flusher,
                                NotificationService $notificationService)
    {
        $this->certificateRepository = $certificateRepository;
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->notificationService = $notificationService;
    }

    public function handle(Command $command)
    {
        $certificate = $this->certificateRepository->getByConfirmToken($command->token);
        $user = $certificate->getUser();

        if($user->getExistsProfile())
            $certificateOld = $user->getProfile()->getCertificate();
        else throw new \DomainException('Невозможно сменить ЭЦП. Профиль пользователя не создан.');

        $certificateOld->archived();
        $certificate->confirmByToken();
        $user->getProfile()->changeCertificate($certificate);

        $this->flusher->flush();

        $message = Message::confirmChangeCertificate($user->getEmail());

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }
}