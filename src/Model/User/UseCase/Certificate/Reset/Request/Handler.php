<?php


namespace App\Model\User\UseCase\Certificate\Reset\Request;


use App\Container\Model\Certificate\CertificateService;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Certificate\CertResetToken;
use App\Model\User\Entity\Certificate\IssuerName;
use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\Certificate\SubjectName;
use App\Model\User\Entity\User\Id as UserId;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Certificate\IssuerConverter\IssuerConverter;
use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverter;
use App\Model\User\Service\ResetTokenGenerator;
use App\Model\User\UseCase\Certificate\Reset\Message;
use App\ReadModel\Certificate\CertificateFetcher;
use App\Services\CryptoPro\CryptoPro;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use App\Services\Tasks\Notification as NotificationService;

class Handler
{
    private $env;
    private $userRepository;
    private $certificateFetcher;
    private $tokenGenerator;
    private $flusher;
    private $router;
    private $notificationService;

    public function __construct(CertificateService $env,
                                UserRepository $userRepository,
                                CertificateFetcher $certificateFetcher,
                                ResetTokenGenerator $tokenGenerator,
                                Flusher $flusher,
                                RouterInterface $router,
                                NotificationService $notificationService)
    {
        $this->env = $env;
        $this->userRepository = $userRepository;
        $this->certificateFetcher = $certificateFetcher;
        $this->tokenGenerator = $tokenGenerator;
        $this->flusher = $flusher;
        $this->router = $router;
        $this->notificationService = $notificationService;
    }


    public function handle(Command $command)
    {
        $cert = CryptoPro::getCertInfoFull($this->env->getHash(), $command->signedData);

        if ($this->certificateFetcher->existsByThumbprintCertificates($cert['thumbprint'])){
            throw new \DomainException("Данный сертификат уже используется");
        }

        $subjectName = new SubjectName(
            $subject = new SubjectConverter(
                $cert['subjectName']
            )
        );

        $issuerName = new IssuerName(
            new IssuerConverter(
                $cert['issuerName']
            )
        );

        if ($userId = $this->certificateFetcher->getUserByInn($subject->toExtractInn()))
            $user = $this->userRepository->get(new UserId($userId));
        else
            throw new UsernameNotFoundException("Пользователь с предоставленным ИНН не найден");

        $certificate = new Certificate(
            CertificateId::next(),
            $user,
            $cert['thumbprint'],
            $subjectName,
            $issuerName,
            new \DateTimeImmutable($cert['validFrom']),
            new \DateTimeImmutable($cert['validTo']),
            new \DateTimeImmutable(),
            Status::wait(),
            $command->signedData,
            new CertResetToken(
                $token = $this->tokenGenerator->generate()->getToken(),
                (new \DateTimeImmutable())->add(new \DateInterval('PT2H'))
            )
        );

        $user->addCertificate($certificate);

        $this->flusher->flush();

        $urlConfirm = $this->router->getContext()->getScheme() . '://' .
            $this->router->getContext()->getHost() .
            $this->router->generate('cert.reset.confirm', ['token' => $token]);

        $message = Message::requestChangeCertificate($user->getEmail(), $urlConfirm);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }
}
