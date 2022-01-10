<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm;


use App\Model\Flusher;
use App\Model\User\Entity\User\User;
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
    private $notificationService;
    private $settingsFetcher;
    private $userRepository;
    private $urlGenerator;

    public function __construct(
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        ProcedureFetcher $procedureFetcher,
        XmlDocumentRepository $xmlDocumentRepository,
        Notification $notificationService,
        SettingsFetcher $settingsFetcher,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator
    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->procedureFetcher = $procedureFetcher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Command $command
     */
    public function approveHandle(Command $command): void{
        $moderator = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->moderatorId));

        $procedureXml = $this->xmlDocumentRepository->get(new Id($command->procedureXmlDocumentId));
        $procedureXml->approve($moderator, $command->clientIp);
        $this->flusher->flush();

        $organizer = $procedureXml->getProcedure()->getOrganizer()->getUser();
        $showProcedureUrl = $this->getBaseUrl().$this->urlGenerator->generate('procedure.show',['procedureId' => $procedureXml->getProcedure()->getId()->getValue()]);;

        $this->sendNotifyApprove(
            $organizer,
            $moderator,
            $procedureXml->getProcedure()->getIdNumber(),
            $procedureXml->getNumber(),
            $showProcedureUrl
        );
    }

    /**
     * @param Command $command
     */
    public function rejectHandle(Command $command): void{
        if ($command->cause === null){
            throw new \DomainException('Не заполнена причина отклонения.');
        }
        $moderator = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->moderatorId));

        $procedureXml = $this->xmlDocumentRepository->get(new Id($command->procedureXmlDocumentId));

        $procedureXml->reject($moderator, $command->clientIp, $command->cause);

        $this->flusher->flush();

        $organizer = $procedureXml->getProcedure()->getOrganizer()->getUser();

        $showProcedureUrl = $this->getBaseUrl().$this->urlGenerator->generate('procedure.show', [
                'procedureId' => $procedureXml->getProcedure()->getId()->getValue()
            ]);

        $this->sendNotifyReject(
            $organizer,
            $moderator,
            $procedureXml->getProcedure()->getIdNumber(),
            $procedureXml->getNumber(),
            $showProcedureUrl,
            $command->cause
        );
    }

    private function getBaseUrl(){
        return $this->urlGenerator->getContext()->getScheme().'://'.$this->urlGenerator->getContext()->getHost();
    }

    private function sendNotifyReject(
        User $organizer,
        User $moderator,
        int $procedureNumber,
        string $xmlDocNumber,
        string $procedureShowUrl,
        string $cause
    ): void {
        $message = Message::procedureReject(
            $organizer->getEmail(),
            $xmlDocNumber,
            $procedureNumber,
            $procedureShowUrl,
            $cause
        );

        // Организатору
        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($organizer, $message);

        // Модератору
        $this->notificationService->sendToOneUser($moderator,$message);
    }

    private function sendNotifyApprove(
        User $organizer,
        User $moderator,
        int $procedureNumber,
        string $xmlDocNumber,
        string $showProcedureUrl
    ): void {
        $message = Message::procedureApprove(
            $organizer->getEmail(),
            $procedureNumber,
            $xmlDocNumber,
            $showProcedureUrl
        );

        // Организатору
        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($organizer, $message);

        $message = Message::procedureApproveModerator(
            $organizer->getEmail(),
            $procedureNumber,
            $xmlDocNumber,
            $showProcedureUrl
        );

        // Модератору
        $this->notificationService->sendToOneUser($moderator,$message);
    }
}