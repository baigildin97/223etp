<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Accredation\Sign;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Id;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\XmlDocument\Id as XmlDocId;
use App\Model\User\Entity\Profile\XmlDocument\IdNumber;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Profile\XmlDocument\NumberGenerator;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    private $userRepository;

    private $urlGenerator;


    public function __construct(
        Flusher $flusher,
        ProfileRepository $profileRepository,
        XmlDocumentFetcher $xmlDocumentFetcher,
        Notification $notificationService,
        SettingsFetcher $settingsFetcher,
        NumberGenerator $numberGenerator,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator
    ){
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->numberGenerator = $numberGenerator;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {

        $profile = $this->profileRepository->get(new Id($command->profileId));
        $findXmlDocuments = $this->xmlDocumentFetcher->existsXmlDocumentByProfileId($profile->getId()->getValue());

        if ($profile->getStatus()->isReplacingEp()){
            $typeStatement = TypeStatement::replacingEp();
        }else{
            if ($findXmlDocuments){
                $typeStatement = TypeStatement::edit();
            }else{
                $typeStatement = TypeStatement::registration();
            }
        }

        $profile->sign(
            $xmlDocId = XmlDocId::next(),
            $idNumber = $this->numberGenerator->next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            $createdAt = new \DateTimeImmutable(),
            StatusTactic::published(),
            $command->clientIp,
            $typeStatement
        );

        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
        $findAccreditationPeriod = $this->settingsFetcher->findDetailByKey(Key::accreditationPeriod());
        $findReplacingEpPeriod = $this->settingsFetcher->findDetailByKey(Key::replacingEpPeriod());
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $showBidUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() . $this->urlGenerator->generate(
                'profile.xml_document.show',
                ['profile_id' => $profile->getId(), 'xml_document_id' => $xmlDocId]
            );


        if ($findXmlDocuments) {
            $message = Message::userSingedModerateProfileRepeat(
                $profile->getUser()->getEmail(),
                $profile->getRepresentative()->getPassport()->getFullName()
            );

            $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
            $this->notificationService->emailToMultipleUsers($usersAdmins, $message);

            if ($typeStatement->isReplacingEp()) {
                // Уведомления в случае замены Электронной подписи.
                $message = Message::userReplacingEp(
                    $profile->getUser()->getEmail(),
                    $idNumber,
                    $showBidUrl,
                    $findReplacingEpPeriod,
                    $createdAt
                );

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($profile->getUser(), $message);
            }
            else {
                // Уведомления в случае редактирование профиля.
                $message = Message::userRepeatSingedProfile(
                    $profile->getUser()->getEmail(),
                    $idNumber,
                    $showBidUrl,
                    $findAccreditationPeriod,
                    $createdAt
                );

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($profile->getUser(), $message);
            }
        } else {
            //Уведомления в случае первичной регистрации
            $message = Message::userSingedModerateProfile(
                $profile->getUser()->getEmail(),
                $profile->getRepresentative()->getPassport()->getFullName()
            );

            $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
            $this->notificationService->sendToMultipleUsers($usersAdmins, $message);

            $message = Message::userSingedProfile(
                $profile->getUser()->getEmail(),
                $profile->getRepresentative()->getPassport()->getFullName(),
                $findSiteName,
                $idNumber,
                $showBidUrl,
                $findAccreditationPeriod,
                $profile->getIncorporatedForm()
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($profile->getUser(), $message);
        }

    }

}