<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Auction\Offer;

use App\Model\User\Entity\Profile\Profile;
use App\Model\Work\Procedure\Entity\Lot\Auction\Auction;
use App\Model\Work\Procedure\Entity\Lot\Auction\Offer\Temp\Temp;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * Class Offer
 * @package App\Model\Work\Procedure\Entity\Lot\Auction\Offer
 * @ORM\Entity()
 * @ORM\Table(name="auction_offers")
 */
class Offer
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="offer_id")
     */
    private $id;

    /**
     * @var Money
     * @ORM\Column(type="money")
     */
    private $cost;

    /**
     * @var Bid
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", cascade={"persist"})
     * @ORM\JoinColumn(name="bid_id", referencedColumnName="id")
     */
    private $bid;

    /**
     * @var Auction
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Auction\Auction", cascade={"persist"})
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id")
     */
    private $auction;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $clientIp;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $sign;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $xml;

   public function __construct(
        Id $id,
        Money $cost,
        Bid $bid,
        Auction $auction,
        string $clientIp,
        \DateTimeImmutable $createdAt,
        string $sign,
        string $hash,
        string $xml
    ) {
        $this->id = $id;
        $this->cost = $cost;
        $this->bid = $bid;
        $this->auction = $auction;
        $this->clientIp = $clientIp;
        $this->createdAt = $createdAt;
        $this->sign = $sign;
        $this->hash = $hash;
        $this->xml = $xml;
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
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getXml(): string
    {
        return $this->xml;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @return Bid
     */
    public function getBid(): Bid
    {
        return $this->bid;
    }

    /**
     * @return Money
     */
    public function getCost(): Money
    {
        return $this->cost;
    }

    /**
     * @return Auction
     */
    public function getAuction(): Auction
    {
        return $this->auction;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }
}