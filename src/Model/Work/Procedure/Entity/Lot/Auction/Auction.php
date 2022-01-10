<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Auction;


use App\Model\User\Entity\Profile\Profile;
use App\Model\Work\Procedure\Entity\Lot\Auction\Offer;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;


/**
 * Class Auction
 * @package App\Model\Work\Procedure\Entity\Lot\Auction
 * @ORM\Entity()
 * @ORM\Table(name="auctions")
 */
class Auction
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="auction_id")
     */
    private $id;

    /**
     * @var Status
     * @ORM\Column(type="auction_status_type")
     */
    private $status;

    /**
     * @var Money
     * @ORM\Column(type="money")
     */
    private $currentCost;

    /**
     * @var Money
     * @ORM\Column(type="money", nullable=true)
     */
    private $finalCost;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $defaultClosedTime;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $closedTime;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", cascade={"persist"})
     * @ORM\JoinColumn(name="winner_id", referencedColumnName="id", nullable=true)
     */
    private $winner;

    /**
     * @var Lot
     * @ORM\OneToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Lot", cascade={"all"})
     * @ORM\JoinColumn(name="lot_id", referencedColumnName="id")
     */
    private $lot;

    /**
     * @var Offer\Offer | ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Auction\Offer\Offer", mappedBy="auction", cascade={"persist"})
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    private $offers;

    /**
     * @var string
     * @ORM\Column(type="string", name="offer_auction_time")
     */
    private $offerAuctionTime;

    /**
     * @var Money
     * @ORM\Column(type="money", name="auction_step")
     */
    private $auctionStep;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="start_trading_date")
     */
    private $startTradingDate;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip")
     */
    private $clientIp;

    public function __construct(
        Id $id,
        Status $status,
        Money $currentCost,
        Lot $lot,
        \DateTimeImmutable $createdAt,
        string $offerAuctionTime,
        Money $auctionStep,
        \DateTimeImmutable $startTradingDate,
        string $clientIp
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->currentCost = $currentCost;
        $this->lot = $lot;
        $this->createdAt = $createdAt;
        $this->offerAuctionTime = $offerAuctionTime;
        $this->auctionStep = $auctionStep;
        $this->startTradingDate = $startTradingDate;
        $this->clientIp = $clientIp;
        $this->offers = new ArrayCollection();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getClosedTime(): \DateTimeImmutable
    {
        return $this->closedTime;
    }

    /**
     * @return Money
     */
    public function getCurrentCost(): Money
    {
        return $this->currentCost;
    }

    /**
     * @return string
     */
    public function getOfferAuctionTime(): string {
        return $this->offerAuctionTime;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDefaultClosedTime(): \DateTimeImmutable
    {
        return $this->defaultClosedTime;
    }

    /**
     * @return Money
     */
    public function getFinalCost(): Money
    {
        return $this->finalCost;
    }

    /**
     * @return Lot
     */
    public function getLot(): Lot
    {
        return $this->lot;
    }

    /**
     * @return Profile
     */
    public function getWinner(): Profile
    {
        return $this->winner;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartTradingDate(): \DateTimeImmutable
    {
        return $this->startTradingDate;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @return Money
     */
    public function getAuctionStep(): Money
    {
        return $this->auctionStep;
    }

    /**
     * @return Offer\Offer[] |ArrayCollection
     */
    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @param Money $money
     */
    public function addCurrentCost(Money $money): void
    {
        $this->currentCost = $this->currentCost->add($money);
    }

    /**
     * Запуск торга
     */
    public function startAuction(){
         if(!$this->status->isWait()){
             throw new \DomainException("Current status is not Wait");
         }

         $this->defaultClosedTime = (new \DateTimeImmutable())->add(new \DateInterval("PT".$this->offerAuctionTime."M"));
         $this->status = Status::active();
         $this->lot->startOfTrading();
    }

    /**
     * Остановка торга
     */
    public function completedAuction(){
        $closedTime = new \DateTimeImmutable();
        $this->status = Status::completed();
        $this->finalCost = $this->currentCost;
        $this->closedTime = $closedTime;

        if($this->offers->count() >= 1 && $this->countConfirmBids() >= 2 ) {
            $this->chooseWinner();

            $i = 1;
            $criteria = new Criteria();
            $criteria->orderBy(['cost' => Criteria::DESC]);
            foreach ($this->getOffers()->matching($criteria) as $offer){
                $bid = $offer->getBid();
                $bid->setPlace($i);
                $i++;
            }
        }
        $this->lot->stopOfTrading();

    }

    /**
     * Определение победителя
     */
    public function chooseWinner(){
        $lastOffer = $this->getOffers()->first()->getBid()->getParticipant();
        $this->winner = $lastOffer;
    }



    /**
     * Возвращает число подвержденных участников в аукционе
     * @return int
     */
    public function countConfirmBids(): int {
        $countConfirmActive = 0;
        foreach ($this->lot->getBids() as $bid){
            if($bid->getConfirmXml() !== null){
                $countConfirmActive++;
            }
        }
        return $countConfirmActive;
    }

    public function edit(string $offerAuctionTime, Money $auctionStep, \DateTimeImmutable  $startTradingDate): void{
        $this->offerAuctionTime = $offerAuctionTime;
        $this->auctionStep = $auctionStep;
        $this->startTradingDate = $startTradingDate;
    }

    public function addOffer(
        Offer\Id $id,
        Money $cost,
        Bid $bid,
        string $clientIp,
        \DateTimeImmutable $dateTimeImmutable,
        string $sign,
        string $hash,
        string $xml
    ): void{
        $this->currentCost = $this->currentCost->add($cost);

        $this->offers->add(
            new Offer\Offer(
                $id,
                $this->currentCost,
                $bid,
                $this,
                $clientIp,
                $dateTimeImmutable,
                $sign,
                $hash,
                $xml
            )
        );
        $this->defaultClosedTime = (new \DateTimeImmutable())->add(new \DateInterval("PT".$this->offerAuctionTime."M"));
    }
}