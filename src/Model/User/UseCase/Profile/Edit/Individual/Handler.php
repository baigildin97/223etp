<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\Individual;

use App\Helpers\Filter;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Representative\Id as RepresentativeId;
use App\Model\User\Entity\Profile\Representative\Passport;
use App\Model\User\Entity\Profile\Representative\Representative;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Services\Tasks\Notification;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

class Handler
{

    private $userRepository;
    private $flusher;
    private $certificateRepository;
    private $profileRepository;
    private $settingsFetcher;
    private $notificationService;
    private $filter;

    public function __construct(UserRepository $userRepository,
                                Flusher $flusher,
                                CertificateRepository $certificateRepository,
                                ProfileRepository $profileRepository,
                                SettingsFetcher $settingsFetcher,
                                Notification $notificationService,
                                Filter $filter
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->certificateRepository = $certificateRepository;
        $this->profileRepository = $profileRepository;
        $this->settingsFetcher = $settingsFetcher;
        $this->notificationService = $notificationService;
        $this->filter = $filter;
    }

    public function handle(Command $command): void
    {
        $user = $this->userRepository->get(new Id($command->userId));
        $profile = $user->getProfile();
        $certificate = $profile->getCertificate();


       /* if ($certificate->getThumbprint() === $profile->getCertificate()->getThumbprint()) {
            throw new \DomainException("Вы не можете изменить на тот же сертификат");
        }

        if (!$certificate->isIndividual()) {
            throw new \DomainException("У данного сертификата тип, не является физическим лицом");
        }

        if ($certificate->getSubjectName()->getInn() !== $profile->getCertificate()->getSubjectName()->getInn()) {
            throw new \DomainException('Инн старого сертификата, не подобен нового');
        }*/

        $arrUserName = $certificate->getSubjectName()->getUserNameExploded();


        $representative = Representative::createIndividual(
            RepresentativeId::next(),
            $profile,
            $this->filter->onlyNumbers($command->phone),
            Passport::createIndividual(
                $command->firstName,
                $command->patronymic,
                $command->lastName,
                $command->passportSeries,
                $command->passportNumber,
                $command->passportIssuer,
                new \DateTimeImmutable($command->passportIssuanceDate),
                $command->citizenship,
                $command->passportUnitCode,
                new \DateTimeImmutable($command->birthDay),
                $command->factCountry,
                $command->factRegion,
                $command->factCity,
                $command->factIndex,
                $command->factStreet,
                $command->factHouse,
                $command->legalCountry,
                $command->legalRegion,
                $command->legalCity,
                $command->legalIndex,
                $command->legalStreet,
                $command->legalHouse,
                $command->inn,
                $command->snils
            ),
            new \DateTimeImmutable(),
            $command->clientIp
        );

        $profile->resetCertificateIndividual($certificate, $representative, $command->webSite);

    /*    $criteriaWhere = new Criteria();
        $expr = new Comparison('status', '=', Status::approve());
        $criteriaWhere->where($expr);

        if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {
            foreach ($profile->getDocuments() as $document) {
                $document->archived();
            }
        }*/

        $this->flusher->flush();

/*        if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {
            $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
            $findAccreditationPeriod = $this->settingsFetcher->findDetailByKey(Key::accreditationPeriod());
            $this->notificationService->createOne(
                NotificationType::resetCertificateProfile(
                    $findSiteName,
                    $profile->getRepresentative()->getPassport()->getFullName(),
                    $findAccreditationPeriod
                ),
                Category::categoryOne(),
                $profile->getUser()
            );

        }*/
    }
}
