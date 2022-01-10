<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\LegalEntity;


use App\Helpers\Filter;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\Organization\Id as OrganizationId;
use App\Model\User\Entity\Profile\Organization\Organization;
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
    private $notificationService;
    private $settingsFetcher;
    private $filter;

    public function __construct(UserRepository $userRepository, Flusher $flusher,
                                CertificateRepository $certificateRepository,
                                ProfileRepository $profileRepository,
                                Notification $notificationService,
                                SettingsFetcher $settingsFetcher,
                                Filter $filter
    )
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->certificateRepository = $certificateRepository;
        $this->profileRepository = $profileRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->filter = $filter;
    }


    public function handle(Command $command): void
    {
        $user = $this->userRepository->get(new Id($command->userId));
        $profile = $user->getProfile();
        $certificate = $profile->getCertificate();

      //  $arrUserName = $certificate->getSubjectName()->getUserNameExploded();

        $passport = Passport::createLegalEntity(
            $command->firstName,
            $command->patronymic,
            $command->lastName,
            $certificate->getSubjectName()->getSnils()
        );
        $passport->setInn($command->representativeInn);

        $representative = Representative::createLegalEntity(
            RepresentativeId::next(),
            $profile,
            $command->position,
            $this->filter->onlyNumbers($command->phone),
            $passport,
            new \DateTimeImmutable(),
            $command->clientIp
        );

        $representative->setPosition($command->position);
        $representative->setConfirmingDocument($command->confirmingDocument);

        $organization = Organization::createLegalEntity(
            OrganizationId::next(),
            $profile,
            $certificate->getSubjectName()->getInn(),
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
            $command->shortTitleOrganization,
            $command->fullTitleOrganization,
            $command->ogrn,
            $command->email,
            $command->kpp,
            new \DateTimeImmutable(),
            $command->clientIp
        );


        $profile->changeProfile(
            $certificate,
            $representative,
            $organization,
            $command->webSite
        );

    /*    $criteriaWhere = new Criteria();
        $expr = new Comparison('status', '=', Status::approve());
        $criteriaWhere->where($expr);

        if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {
            foreach ($profile->getDocuments() as $document) {
                $document->archived();
            }
        }*/
        $this->flusher->flush();


       /* if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {

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
