<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\ChangeCertificate\IndividualEntrepreneur;

use App\Helpers\Filter;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
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
use \App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\UseCase\Profile\ChangeCertificate\Message;
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

        $certificate = $this->certificateRepository->get(new CertificateId($command->certificate));
        $profile = $user->getProfile();

        if ($profile->getCertificate()->getSubjectName()->getInn() !== $certificate->getSubjectName()->getInn()){
            throw new \DomainException('Доступ запрещен.');
        }

        if ($certificate->getThumbprint() === $profile->getCertificate()->getThumbprint()) {
            throw new \DomainException("Вы не можете изменить на тот же сертификат");
        }

        if (!$certificate->isIndividualEntrepreneur()) {
            throw new \DomainException("У данного сертификата тип, не является индивидуальным предпринимателем");
        }


        $profile->changeCertificate($certificate);

        /*$criteriaWhere = new Criteria();
        $expr = new Comparison('status', '=', Status::approve());
        $criteriaWhere->where($expr);
        if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {
            foreach ($profile->getDocuments() as $document) {
                $document->archived();
            }
        }*/
        $this->flusher->flush();

        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::resetCertificateProfileModerate($profile->getUser()->getEmail(),
            $profile->getRepresentative()->getPassport()->getFullName());

        //$this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }
}
