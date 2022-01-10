<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Notification\Sign;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\XmlDocument\Id;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocumentRepository;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class Handler
{
    private $flusher;
    private $xmlDocumentRepository;
    private $notificationService;
    private $settingsFetcher;
    private $urlGenerator;

    public function __construct(
        Flusher $flusher,
        XmlDocumentRepository $xmlDocumentRepository,
        Notification $notificationService,
        SettingsFetcher $settingsFetcher,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Command $command): void
    {
        $xml = $this->xmlDocumentRepository->get(new Id($command->notificationId));
        $xml->signAndPublished($command->sign, $command->hash, new \DateTimeImmutable());
        $this->flusher->flush();


        $findSiteName = $this->settingsFetcher->findDetailByKey(Key::nameService());
        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() .
            $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $xml->getProcedure()->getId()]);

        $message = Message::procedurePublished(
            $xml->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $findSiteName,
            $xml->getProcedure()->getIdNumber(),
            $showProcedureUrl
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($xml->getProcedure()->getOrganizer()->getUser(), $message);
    }
}