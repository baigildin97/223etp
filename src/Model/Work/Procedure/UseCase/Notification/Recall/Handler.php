<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Notification\Recall;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocumentRepository;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    /**
     * @var Flusher
     */
    public $flusher;

    /**
     * @var ProcedureRepository
     */
    public $procedureRepository;

    /**
     * @var XmlDocumentRepository
     */
    public $xmlDocumentRepository;

    /**
     * @var Notification
     */
    public $notificationService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param XmlDocumentRepository $xmlDocumentRepository
     * @param Notification $notificationService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                XmlDocumentRepository $xmlDocumentRepository,
                                Notification $notificationService,
                                UrlGeneratorInterface $urlGenerator
    ) {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Command $command){
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));
        $procedure->cancelPublished();

        $xmlDoc = $this->xmlDocumentRepository->get(new \App\Model\Work\Procedure\Entity\XmlDocument\Id($command->notificationId));
        $xmlDoc->cancelModeratePublished($command->clientIp);

        $this->flusher->flush();

        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() .
            $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $procedure->getId()->getValue()]);

        $message = Message::cancellingPublication(
            $procedure->getOrganizer()->getUser()->getEmail(),
            $xmlDoc->getNumber()->getValue(),
            $xmlDoc->getProcedure()->getIdNumber(),
            $showProcedureUrl
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($xmlDoc->getProcedure()->getOrganizer()->getUser(), $message);
    }
}