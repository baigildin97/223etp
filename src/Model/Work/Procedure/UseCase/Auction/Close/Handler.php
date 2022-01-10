<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Close;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Auction\AuctionRepository;
use App\Model\Work\Procedure\Entity\Lot\Auction\Id;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var AuctionRepository
     */
    private $auctionRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param AuctionRepository $auctionRepository
     */
    public function __construct(Flusher $flusher, AuctionRepository $auctionRepository){
        $this->flusher = $flusher;
        $this->auctionRepository = $auctionRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $auction = $this->auctionRepository->get(new Id($command->auctionId));
        $auction->completedAuction();
        $this->flusher->flush();
    }

}