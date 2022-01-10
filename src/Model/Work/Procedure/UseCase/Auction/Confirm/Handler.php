<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Confirm;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;

class Handler
{
    /**
     * @var Flusher
     */
    public $flusher;

    /**
     * @var BidRepository
     */
    public $bidRepository;

    public function __construct(Flusher $flusher, BidRepository $bidRepository){
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
    }

    public function handle(Command $command){
        $bid = $this->bidRepository->get(new Id($command->bidId));
        $bid->confirm($command->xml, $command->sign, $command->hash);
        $this->flusher->flush();

    }
}