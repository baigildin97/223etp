<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Sign;

use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\XmlDocument\IdNumber;
use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\Model\Work\Procedure\Entity\XmlDocument\StatusTactic;
use App\Model\Work\Procedure\Services\Procedure\XmlDocument\NumberGenerator;
use App\ReadModel\Procedure\Lot\Document\DocumentFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Procedure\XmlDocument\XmlDocumentFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * @var ProcedureFetcher
     */
    private $procedureFetcher;

    /**
     * @var DocumentFetcher
     */
    private $documentFetcher;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    private $urlGenerator;

    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                ProcedureFetcher $procedureFetcher,
                                UserRepository $userRepository,
                                DocumentFetcher $documentFetcher,
                                Notification $notificationService,
                                XmlDocumentFetcher $xmlDocumentFetcher,
                                NumberGenerator $numberGenerator,
                                UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->procedureFetcher = $procedureFetcher;
        $this->userRepository = $userRepository;
        $this->documentFetcher = $documentFetcher;
        $this->notificationService = $notificationService;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->numberGenerator = $numberGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));

        $numberGenerator = $this->numberGenerator->next();


        $procedure->createNotice(
            \App\Model\Work\Procedure\Entity\XmlDocument\Id::next(),
            $numberGenerator,
            Status::moderate(),
            \App\Model\Work\Procedure\Entity\XmlDocument\Type::notifyPublish(),
            $command->xml,
            $command->hash,
            new \DateTimeImmutable(),
            StatusTactic::published(),
            $command->clientIp
        );
        $this->flusher->flush();

        $findXmlDocuments = $this->xmlDocumentFetcher->existsXmlDocumentByProcedureId($procedure->getId()->getValue());


        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();
        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() .
            $this->urlGenerator->generate('procedure.show', ['procedureId' => $procedure->getId()]);

        if ($findXmlDocuments) {
            //Повторная модерация
            $message = Message::procedureModerateRepeat(
                $procedure->getOrganizer()->getUser()->getEmail(),
                $procedure->getIdNumber(),
                $showProcedureUrl
            );

            $this->notificationService->sendToOneUser($procedure->getOrganizer()->getUser(), $message);
            $this->notificationService->emailToOneUser($message);

            $this->notificationService->sendToMultipleUsers($usersAdmins, $message);

        } else {
            //Первичная модерация
            $message = Message::procedureModerate(
                $procedure->getOrganizer()->getUser()->getEmail(),
                $procedure->getIdNumber(),
                $showProcedureUrl
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($procedure->getOrganizer()->getUser(), $message);

            $message = Message::procedureCreate(
                $procedure->getOrganizer()->getUser()->getEmail(),
                $procedure->getIdNumber(),
                $showProcedureUrl
            );

            $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
            $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        }

    }

}