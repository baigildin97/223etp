<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload\Sign;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Document\DocumentRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Document\Id;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{

    private $flusher;

    private $documentRepository;

    private $bidRepository;

    private $profileRepository;


    private $notificationService;

    private $urlGenerator;

    public function __construct(Flusher $flusher,
                                DocumentRepository $documentRepository,
                                BidRepository $bidRepository,
                                ProfileRepository $profileRepository,
                                Notification $notificationService,
                                UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->documentRepository = $documentRepository;
        $this->bidRepository = $bidRepository;
        $this->profileRepository = $profileRepository;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Command $command): void
    {
        $profile = $this->profileRepository->getByUser(new \App\Model\User\Entity\User\Id($command->userId));
        $bid = $this->bidRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Bid\Id($command->bidId));
        if ($command->documentId === 'DEPOSIT_AGREEMENT') {
            if ($profile->getRole()->isOrganizer()) {
                $bid->signDepositAgreementOrganizer($command->sign);

            } elseif ($bid->getParticipant()->getRole()->isEqual($profile->getRole())) {
                $bid->signDepositAgreementParticipant($command->sign);
            }

        } else {
            $document = $this->documentRepository->get(new Id($command->documentId));
            $document->sign($command->sign, $command->clientIp);
        }

        $this->flusher->flush();


        if ($command->documentId === 'DEPOSIT_AGREEMENT') {
            if ($profile->getRole()->isOrganizer()) {
                $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
                    $this->urlGenerator->getContext()->getHost();
                $showBidUrl = $baseUrl . $this->urlGenerator->generate('bid.history', ['bidId' => $bid->getId()->getValue()]);
                $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                        ['procedureId' => $bid->getLot()->getProcedure()->getId()]);

                $user = $profile->getUser();
                $message = Message::signedDepositAgreementOrganizer($user->getEmail(), $bid->getNumber(), $showBidUrl,
                    $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($user, $message);

                $user = $bid->getParticipant()->getUser();
                $message = Message::signedDepositAgreement(
                    $user->getEmail(),
                    $bid->getLot()->getProcedure()->getIdNumber(),
                    $showProcedureUrl
                );

                $this->notificationService->emailToOneUser($message);
                $this->notificationService->sendToOneUser($user, $message);
            }
        }


    }

}