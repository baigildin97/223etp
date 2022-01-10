<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Bet;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Auction;
use App\Model\Work\Procedure\Entity\Lot\Auction\Offer\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid;
use App\Services\CryptoPro\CryptoPro;
use App\Services\Hash\Streebog;
use Doctrine\Common\Collections\Criteria;
use Money\Money;

class Handler
{
    /**
     * @var Flusher
     */
    public $flusher;

    /**
     * @var Auction\AuctionRepository
     */
    public $auctionRepository;

    /**
     * @var Bid\BidRepository
     */
    public $bidRepository;

    /**
     * @var Streebog
     */
    public $streebog;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param Auction\AuctionRepository $auctionRepository
     * @param Bid\BidRepository $bidRepository
     * @param Streebog $streebog
     */
    public function  __construct(
        Flusher $flusher,
        Auction\AuctionRepository $auctionRepository,
        Bid\BidRepository $bidRepository,
        Streebog $streebog
    ){
        $this->flusher = $flusher;
        $this->auctionRepository = $auctionRepository;
        $this->bidRepository = $bidRepository;
        $this->streebog = $streebog;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void {
        $bid = $this->bidRepository->get(new Bid\Id($command->bidId));

        if (!$this->streebog->verify($command->xml,$command->hash)){
            throw new \DomainException('Не удалось подтвердить подпись.');
        }

        if ($bid->getParticipant()->getId()->getValue() !== $command->profileId){
            throw new \DomainException('Доступ запрещен.');
        }

        if ($bid->getLot()->getAuction()->getDefaultClosedTime() <= new \DateTimeImmutable()){
            throw new \DomainException('Время выставления ценового предложения истекло.');
        }


        $auction = $this->auctionRepository->get(new Auction\Id($command->auctionId));

        $offers = $auction->getOffers();

        $criteria = new Criteria();
        $criteria->orderBy(['createdAt' => Criteria::DESC]);
        $lastOffer = $offers->matching($criteria)->first();


        if ($lastOffer){
            if ($lastOffer->getBid()->getParticipant()->getId()->getValue() === $command->profileId){
                throw new \DomainException('Вы уже предложили наивысшую цену контракта.');
            }
        }

        $auction->addOffer(
            Id::next(),
            Money::RUB($command->cost * 100),
            $bid,
            $command->clientIp,
            new \DateTimeImmutable(),
            $command->sign,
            $command->hash,
            $command->xml
        );

        $this->flusher->flush();
    }

}