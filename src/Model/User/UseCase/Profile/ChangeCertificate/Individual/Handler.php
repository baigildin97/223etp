<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\ChangeCertificate\Individual;

use App\Helpers\Filter;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
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

        if (!$certificate->isIndividual()) {
            throw new \DomainException("У данного сертификата тип, не является физическим лицом");
        }

        $profile->changeCertificate($certificate);

   /*     $criteriaWhere = new Criteria();
        $expr = new Comparison('status', '=', Status::approve());
        $criteriaWhere->where($expr);

        if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {
            foreach ($profile->getDocuments() as $document) {
                $document->archived();
            }
        }*/
        $this->flusher->flush();

        // Клиенту
        $clientMessage = Message::resetCertificateUser(
            $profile->getUser()->getEmail()
        );
        $this->notificationService->emailToOneUser($clientMessage);
        $this->notificationService->sendToOneUser($profile->getUser() ,$clientMessage);


        // Админам
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::resetCertificateModerator(
            $profile->getUser()->getEmail(),
            $profile->getFullName()
        );
        //$this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }

}
