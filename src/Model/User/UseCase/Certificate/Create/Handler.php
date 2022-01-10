<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Certificate\Create;



use App\Container\Model\Certificate\CertificateService;
use App\Helpers\Filter;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Certificate\IssuerName;
use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\Certificate\SubjectName;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Certificate\IssuerConverter\IssuerConverter;
use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverter;
use App\ReadModel\Certificate\CertificateFetcher;
use App\Services\CryptoPro\CryptoPro;
use Psr\Log\LoggerInterface;
use App\Model\User\Entity\User\Id as UserId;
use Symfony\Component\Dotenv\Dotenv;

class Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    private $env;

    /**
     * @var CertificateFetcher
     */
    private $certificateFetcher;

    private $dotenv;

    public function __construct(
        UserRepository $userRepository,
        Flusher $flusher,
        CertificateFetcher $certificateFetcher,
        CertificateService $env
    ) {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->certificateFetcher = $certificateFetcher;
        $this->env = $env;
    }

    /**
     * @param Command $command
     * @throws \Doctrine\DBAL\Exception
     */
    public function handle(Command $command): void {
        $cert = CryptoPro::getCertInfoFull($this->env->getHash(), $command->sign);

        if ($this->certificateFetcher->existsByStatusCertificates(
            Status::active(),
            \App\ReadModel\Certificate\Filter\Filter::forUserId($command->userId))
        ){
            throw new \DomainException("Возможна активация и использование на площадке только одного сертификата ЭП конкретного пользователя.");
        }

        if ($this->certificateFetcher->existsByThumbprintCertificates($cert['thumbprint'])){
            throw new \DomainException("Данный сертификат уже используется");
        }

        $user = $this->userRepository->get(new UserId($command->userId));

        $subjectName = new SubjectName(
            new SubjectConverter(
                $cert['subjectName']
            )
        );

        $issuerName = new IssuerName(
            new IssuerConverter(
                $cert['issuerName']
            )
        );


        $certificate = new Certificate(
            CertificateId::next(),
            $user,
            $cert['thumbprint'],
            $subjectName ,
            $issuerName,
            new \DateTimeImmutable($cert['validFrom']),
            new \DateTimeImmutable($cert['validTo']),
            new \DateTimeImmutable(),
            Status::active(),
            $command->sign
        );

        $user->addCertificate($certificate);

        $this->flusher->flush();
    }

}