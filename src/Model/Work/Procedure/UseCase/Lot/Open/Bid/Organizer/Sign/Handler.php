<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign;


use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid\TempStatus;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Category;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Status;
use App\Services\Tasks\Notification;

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
     * @var Notification
     */
    private $notificationService;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param BidRepository $bidRepository
     * @param Notification $notificationService
     */
    public function __construct(Flusher $flusher,
                                BidRepository $bidRepository,
                                Notification $notificationService
    )
    {
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {

        $bid = $this->bidRepository->get(new Id($command->bidId));


        $bid->signOrganizer(
            \App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Id::next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            Category::recall(),
            new \DateTimeImmutable(),
            $command->client_ip
        );
        $this->flusher->flush();





    }
}