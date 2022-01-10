<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Moderator\Action;


use App\Container\Model\SubscribeTariff\SubscribeTariffFactory;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariffRepository;
use App\Model\User\Entity\Profile\XmlDocument\Id;
use App\Model\User\Entity\Profile\XmlDocument\XmlDocumentRepository;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationRepository;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Profile\Payment\InvoiceNumberGenerator;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Model\User\Entity\User\Notification\Id as NotificationId;
use App\Model\User\Entity\User\Notification\Notification;
use App\Services\Tasks\Notification as NotificationService;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Accredation\Moderator\Action
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var XmlDocumentRepository
     */
    private $xmlDocumentRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var SubscribeTariffRepository
     */
    private $subscribeTariffRepository;

    /**
     * @var \App\Model\User\UseCase\Profile\Tariff\Buy\Handler
     */
    private $buyHandler;

    /**
     * @var SubscribeTariffFactory
     */
    private $subscribeTariffFactory;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param XmlDocumentRepository $xmlDocumentRepository
     * @param UserRepository $userRepository
     * @param SubscribeTariffRepository $subscribeTariffRepository
     * @param \App\Model\User\UseCase\Profile\Tariff\Buy\Handler $BuyHandler
     * @param SubscribeTariffFactory $subscribeTariffFactory
     * @param NotificationService $notificationService
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        Flusher $flusher,
        XmlDocumentRepository $xmlDocumentRepository,
        UserRepository $userRepository,
        SubscribeTariffRepository $subscribeTariffRepository,
        \App\Model\User\UseCase\Profile\Tariff\Buy\Handler $BuyHandler,
        SubscribeTariffFactory $subscribeTariffFactory,
        NotificationService $notificationService,
        SettingsFetcher $settingsFetcher
    )
    {
        $this->flusher = $flusher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->userRepository = $userRepository;
        $this->subscribeTariffRepository = $subscribeTariffRepository;
        $this->buyHandler = $BuyHandler;
        $this->subscribeTariffFactory = $subscribeTariffFactory;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handleApprove(Command $command): void
    {
        $xmlDocument = $this->xmlDocumentRepository->get(new Id($command->xmlDocumentId));
        $moderator = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->user_id));

        $xmlDocument->approve($moderator, $command->clientIp);

        if ($xmlDocument->getProfile()->getPayment() === null) {
            $xmlDocument->getProfile()->createPaymentAccount((new InvoiceNumberGenerator())->generate());
        }

        if($xmlDocument->getProfile()->getSubscribeTariff() === null){
            $commandTariff = new \App\Model\User\UseCase\Profile\Tariff\Buy\Command(
                $this->subscribeTariffFactory->create(),
                $xmlDocument->getProfile()->getUser()->getId()->getValue());
            $this->buyHandler->handle($commandTariff);
        }
        $this->flusher->flush();

        if ($xmlDocument->getType()->isRegistration()){
            $this->sendProfileSuccessRegistrationNotify($xmlDocument->getProfile());
        }

        if ($xmlDocument->getType()->isEdit()){
            $this->sendProfileSuccessEditNotify($xmlDocument->getProfile());
        }

        if ($xmlDocument->getType()->isReplacingEp()){
            $this->sendProfileReplacingEpNotify($xmlDocument->getProfile());
        }
    }

    /**
     * @param Command $command
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handleReject(Command $command): void{
        if ($command->cause === null) {
            throw new \DomainException('Укажите причину отклонения заявки.');
        }
        $xmlDocument = $this->xmlDocumentRepository->get(new Id($command->xmlDocumentId));
        $moderator = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->user_id));

        $xmlDocument->reject(
            $moderator,
            $command->cause,
            $command->clientIp
        );

        $this->flusher->flush();

        if ($xmlDocument->getType()->isRegistration()){
            $this->sendProfileFailedRegistrationNotify($xmlDocument->getProfile(), $command->cause, $xmlDocument->getIdNumber(), $xmlDocument->getCreatedAt());
        }

        if ($xmlDocument->getType()->isEdit()){
            $this->sendProfileFailedEditNotify($xmlDocument->getProfile(), $command->cause);
        }
    }

    /**
     * @param Profile $profile
     * @param string $cause
     * @param int $bidId
     * @param \DateTimeImmutable $createdAt
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function sendProfileFailedRegistrationNotify(Profile $profile, string $cause, int $bidId, \DateTimeImmutable $createdAt): void {
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
        $fullName = $profile->getRepresentative()->getPassport()->getFullName();

        $message = Message::profileRejectUser($profile->getUser()->getEmail(), $findSiteName, $bidId,
            $cause, $createdAt);

        $this->notificationService->sendToOneUser($profile->getUser(), $message);
        $this->notificationService->emailToOneUser($message);

        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::profileRejectModerator($profile->getUser()->getEmail(), $fullName, $cause);

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
    }

    /**
     * @param Profile $profile
     * @param string $cause
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function sendProfileFailedEditNotify(Profile $profile, string $cause): void {
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
        $fullName = $profile->getRepresentative()->getPassport()->getFullName();

        $message = Message::profileEditRejectUser($profile->getUser()->getEmail(), $findSiteName, $cause);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($profile->getUser(), $message);

        $message = Message::profileEditRejectModerator($profile->getUser()->getEmail(), $fullName, $cause);
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }

    /**
     * @param Profile $profile
     */
    private function sendProfileSuccessRegistrationNotify(Profile $profile): void {
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
        $findFullNameOrganization = $this->settingsFetcher->findDetailByKey(Key::fullNameOrganization());

        if ($profile->getRole()->isOrganizer()) {
            $message = Message::profileSuccessRegisterOrganization($profile->getUser()->getEmail(),
                $findSiteName, $findFullNameOrganization);
        }else {
            $message = Message::profileSuccessRegister($profile->getUser()->getEmail(), $findSiteName);
        }
        $this->notificationService->sendToOneUser($profile->getUser(), $message);
        $this->notificationService->emailToOneUser($message);

        $message = Message::profileSuccessRegisterModerator($profile->getUser()->getEmail(),
            $profile->getRepresentative()->getPassport()->getFullName());
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
    }

    /**
     * @param Profile $profile
     */
    private function sendProfileSuccessEditNotify(Profile $profile): void {
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $message = Message::profileSuccessEdit($profile->getUser()->getEmail(), $findSiteName);

        $this->notificationService->sendToOneUser($profile->getUser(), $message);
        $this->notificationService->emailToOneUser($message);

        $message = Message::profileSuccessEditModerator($profile->getUser()->getEmail(),
            $profile->getRepresentative()->getPassport()->getFullName());
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }

    /**
     * @param Profile $profile
     * Уведомления после одобрения модератором, заявления на замену ЭП
     */
    private function sendProfileReplacingEpNotify(Profile $profile): void {
        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $message = Message::profileSuccessEdit($profile->getUser()->getEmail(), $findSiteName);

        $this->notificationService->sendToOneUser($profile->getUser(), $message);
        $this->notificationService->emailToOneUser($message);

        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::profileReplaceEpModerator($profile->getUser()->getEmail(),
            $profile->getRepresentative()->getPassport()->getFullName());

        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }

}