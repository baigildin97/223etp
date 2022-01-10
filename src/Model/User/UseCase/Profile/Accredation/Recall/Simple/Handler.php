<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Recall\Simple;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\XmlDocument\Id;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\Model\User\Entity\Profile\XmlDocument\XmlDocument;
use App\Model\User\Entity\Profile\XmlDocument\XmlDocumentRepository;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Profile\XmlDocument\NumberGenerator;
use App\Services\Tasks\Notification;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * @var Notification
     */
    private $notificationService;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    /**
     * @var
     */
    private $userRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param XmlDocumentRepository $xmlDocumentRepository
     * @param Notification $notificationService
     * @param ProfileRepository $profileRepository
     * @param NumberGenerator $numberGenerator
     */
    public function __construct(
        Flusher $flusher,
        XmlDocumentRepository $xmlDocumentRepository,
        Notification $notificationService,
        ProfileRepository $profileRepository,
        NumberGenerator $numberGenerator,
        UserRepository $userRepository
    ){
        $this->flusher = $flusher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->profileRepository = $profileRepository;
        $this->numberGenerator = $numberGenerator;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {

        $xmlDocument = $this->xmlDocumentRepository->get(new Id($command->xmlDocumentId));

        $xmlDocument->recall($command->clientIp);

        $profile = $this->profileRepository->get(new \App\Model\User\Entity\Profile\Id($command->profileId));

        if ($xmlDocument->getType()->isReplacingEp()){
            $status = \App\Model\User\Entity\Profile\Status::replacingEp();
        }

        if ($xmlDocument->getType()->isEdit() or $xmlDocument->getType()->isRegistration()){
            $status = \App\Model\User\Entity\Profile\Status::wait();
        }




        $profile->recall(
            Id::next(),
            $this->numberGenerator->next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            new \DateTimeImmutable(),
            StatusTactic::published(),
            TypeStatement::recall(),
            $status
        );

        $this->flusher->flush();
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        // TODO - нужно хэндлить ошибки
        if ($xmlDocument->getType()->isRegistration()){
            $this->sendProfileRegistrationXmlDocumentRecallNotify($xmlDocument, $usersAdmins);
        }

        if ($xmlDocument->getType()->isEdit()){
            $this->sendProfileEditXmlDocumentRecallNotify($xmlDocument, $usersAdmins);
        }

        if ($xmlDocument->getType()->isReplacingEp()){
            $this->sendReplacingEpXmlDocumentRecallNotify($xmlDocument, $usersAdmins);
        }
    }


    private function sendProfileRegistrationXmlDocumentRecallNotify(XmlDocument $xmlDocument, array $usersAdmins): void {
        $user = $xmlDocument->getProfile()->getUser();
        $message = Message::profileXmlDocumentRegistrationRecallUser($user->getEmail(),
            $xmlDocument->getIdNumber(), $xmlDocument->getCreatedAt());

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);


        $message = Message::profileXmlDocumentRegistrationRecallModerator(
            $user->getEmail(),
            $user->getProfile()->getFullName(),
            $xmlDocument->getIdNumber(),
            $xmlDocument->getCreatedAt()
        );

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);

    }

    private function sendProfileEditXmlDocumentRecallNotify(XmlDocument $xmlDocument, array $usersAdmins): void {
        $user = $xmlDocument->getProfile()->getUser();
        $message = Message::profileXmlDocumentEditRecallUser($user->getEmail(),
            $xmlDocument->getIdNumber(), $xmlDocument->getCreatedAt());

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);


        $message = Message::profileXmlDocumentEditRecallModerator(
            $user->getEmail(),
            $user->getProfile()->getFullName(),
            $xmlDocument->getIdNumber(),
            $xmlDocument->getCreatedAt()
        );

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
    }


    private function sendReplacingEpXmlDocumentRecallNotify(XmlDocument $xmlDocument, array $usersAdmins): void {
        $user = $xmlDocument->getProfile()->getUser();
        $message = Message::profileXmlDocumentReplacingEp($user->getEmail(),
            $xmlDocument->getIdNumber(), $xmlDocument->getCreatedAt());

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);


        $message = Message::profileXmlDocumentReplacingEpModerator(
            $user->getEmail(),
            $user->getProfile()->getFullName(),
            $xmlDocument->getIdNumber(),
            $xmlDocument->getCreatedAt()
        );

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
    }
}