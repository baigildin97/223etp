<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Action;


use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var BidRepository
     */
    private $bidRepository;

    /**
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param BidRepository $bidRepository
     * @param BidFetcher $bidFetcher
     * @param Notification $notificationService
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        Flusher $flusher,
        BidRepository $bidRepository,
        BidFetcher $bidFetcher,
        Notification $notificationService,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
        $this->bidFetcher = $bidFetcher;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Command $command
     */
    public function handleApprove(Command $command): void
    {
        $bid = $this->bidRepository->get(new Id($command->bidId));

        $bid->approve();
       // $this->flusher->flush();


        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();
        $showBidUrl = $baseUrl . $this->urlGenerator->generate('bid.show', ['bidId' => $bid->getId()->getValue()]);
        $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $bid->getLot()->getProcedure()->getId()]);

        $user = $bid->getLot()->getProcedure()->getOrganizer()->getUser();
        $message = Message::organizerApproveBid($user->getEmail(), $bid->getNumber(),
            $showBidUrl, $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);

        $user = $bid->getParticipant()->getUser();
        $message = Message::participantApproveBid($user->getEmail(), $bid->getNumber(),
            $showBidUrl, $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }

    public function handleReject(Command $command): void
    {
        $bid = $this->bidRepository->get(new Id($command->bidId));

        if ($command->cause === null) {
            throw new \DomainException('Заполните причину.');
        }
        $bid->reject($command->cause);
        $this->flusher->flush();


        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();
        $showBidUrl = $baseUrl . $this->urlGenerator->generate('bid.history', ['bidId' => $bid->getId()->getValue()]);
        $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $bid->getLot()->getProcedure()->getId()]);

        $user = $bid->getLot()->getProcedure()->getOrganizer()->getUser();
        $message = Message::organizerRejectBid($user->getEmail(), $bid->getNumber(),
            $showBidUrl, $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);

        $user = $bid->getParticipant()->getUser();
        $message = Message::participantRejectBid($user->getEmail(), $bid->getNumber(),
            $showBidUrl, $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }

}