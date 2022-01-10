<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity;


use App\Helpers\Filter;
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


    public function handle(Command $command): void{
        $user = $this->userRepository->get(new Id($command->userId));

        $certificate = $this->certificateRepository->get(new CertificateId($command->certificate));
        $profile = $user->getProfile();

  /*      if ($profile->getCertificate()->getSubjectName()->getInn() !== $certificate->getSubjectName()->getInn()){
            throw new \DomainException('Доступ запрещен.');
        }*/

        if (!$certificate->isLegalEntity()) {
            throw new \DomainException("У данного сертификата тип, не является юридическим лицом");
        }

        if ($certificate->getThumbprint() === $profile->getCertificate()->getThumbprint()) {
            throw new \DomainException("Сертификат уже привязан.");
        }

        $profile->changeCertificate($certificate);

       /* $criteriaWhere = new Criteria();
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


  /*      if ($profile->getXmlDocuments()->matching($criteriaWhere)->count() >= 1) {

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
