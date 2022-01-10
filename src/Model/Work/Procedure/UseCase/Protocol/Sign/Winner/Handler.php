<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner;

use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Protocol\ProtocolRepository;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Procedure\Lot\Protocol\ProtocolFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class Handler
{

    private $flusher;

    private $userRepository;

    private $procedureRepository;

    private $protocolFetcher;

    private $protocolRepository;

    private $lotRepository;

    private $notificationService;

    private $urlGenerator;

    public function __construct(Flusher $flusher,
                                UserRepository $userRepository,
                                ProcedureRepository $procedureRepository,
                                ProtocolFetcher $protocolFetcher,
                                ProtocolRepository $protocolRepository,
                                LotRepository $lotRepository,
                                Notification $notificationService,
                                UrlGeneratorInterface $urlGenerator
    ) {
        $this->flusher = $flusher;
        $this->userRepository = $userRepository;
        $this->procedureRepository = $procedureRepository;
        $this->protocolFetcher = $protocolFetcher;
        $this->protocolRepository = $protocolRepository;
        $this->lotRepository = $lotRepository;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Подписание победителем
     * @param Command $command
     */
    public function handle(Command $command): void {
        $lot = $this->lotRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Id($command->lotId));
        $user = $this->userRepository->get(new \App\Model\User\Entity\User\Id($command->userId));
        if($command->userId !== $lot->getAuction()->getWinner()->getUser()->getId()->getValue()){
            throw new \DomainException("Only the winner can sign");
        }

        $procedure = $this->procedureRepository->get(new Id($command->procedureId));
        $thumbprint = $user->getProfile()->getCertificate()->getThumbprint();

        $protocol = $this->protocolFetcher->findDetail($command->protocolId);
        $xmlDocument = $this->protocolRepository->get(new \App\Model\Work\Procedure\Entity\Protocol\Id($command->protocolId));

        $xmlDocument->getXmlDocument()->signedParticipant(
            $command->hash,
            $command->sign,
            $thumbprint,
            new \DateTimeImmutable(),
        );

        $type = new Type($protocol->type);

        $procedure->signedWinner();
        $this->flusher->flush();

        $showProcedureNumber = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() .
            $this->urlGenerator->generate('procedure.show', ['procedureId' => $procedure->getId()]);

        if($type->isResultProtocol()){
            $message = Message::organizerSignedProtocolWinner(
                $procedure->getOrganizer()->getUser()->getEmail(),
                $lot->getFullNumber(),
                $showProcedureNumber
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($procedure->getOrganizer()->getUser(), $message);

            $message = Message::participantSignedProtocolWinner(
                $user->getEmail(),
                $lot->getFullNumber(),
                $showProcedureNumber
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($user, $message);
        }


    }

}