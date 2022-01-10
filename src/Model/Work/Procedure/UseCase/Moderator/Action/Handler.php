<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Action;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\XmlDocument\Id;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocumentRepository;
use App\Model\Work\Procedure\UseCase\Moderator\Message;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    private $flusher;
    private $procedureRepository;
    private $procedureFetcher;
    private $xmlDocumentRepository;
    private $bidRejectedEmailSender;
    private $approveEmailSender;
    private $rejectEmailSender;
    private $notificationService;
    private $settingsFetcher;
    private $urlGenerator;
    private $userRepository;

    public function __construct(
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        ProcedureFetcher $procedureFetcher,
        XmlDocumentRepository $xmlDocumentRepository,
        Notification $notificationService,
        SettingsFetcher $settingsFetcher,
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository
    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->procedureFetcher = $procedureFetcher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command $command
     */
    public function approveHandle(Command $command): void{

        $procedureXml = $this->xmlDocumentRepository->get(new Id($command->procedureXmlDocumentId));
        $procedureXml->approve();
        $this->flusher->flush();

        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
                            $this->urlGenerator->getContext()->getHost() .
                            $this->urlGenerator->generate(
                                'procedure.show',
                                ['procedureId' => $procedureXml->getProcedure()->getId()]
                            );

        $message = Message::procedureApprove(
            $procedureXml->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $procedureXml->getProcedure()->getIdNumber(),
            $showProcedureUrl,
            $findSiteName
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($procedureXml->getProcedure()->getOrganizer()->getUser(), $message);

        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::procedureApproveModerator(
            $procedureXml->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $procedureXml->getProcedure()->getIdNumber(),
            $showProcedureUrl
        );

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }

    /**
     * @param Command $command
     */
    public function rejectHandle(Command $command): void{
        if ($command->cause === null){
            throw new \DomainException('Не заполнена причина отклонения.');
        }

        $procedureXml = $this->xmlDocumentRepository->get(new Id($command->procedureXmlDocumentId));
        $procedureXml->reject($command->cause);

        $this->flusher->flush();

        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());

        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
                            $this->urlGenerator->getContext()->getHost() .
                            $this->urlGenerator->generate(
                                'procedure.show',
                                ['procedureId' => $procedureXml->getProcedure()->getId()]
                            );

        $message = Message::procedureReject(
            $procedureXml->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $procedureXml->getProcedure()->getIdNumber(),
            $showProcedureUrl,
            $findSiteName,
            $command->cause
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($procedureXml->getProcedure()->getOrganizer()->getUser(), $message);

        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $message = Message::procedureApproveModerator(
            $procedureXml->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $procedureXml->getProcedure()->getIdNumber(),
            $showProcedureUrl
        );

        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
    }
}