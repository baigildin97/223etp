<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Auction\Bet;


use Symfony\Component\Validator\Constraints as Assert;

class Command{

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $profileId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $auctionId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $cost;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xml;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $bidId;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $profileId
     * @param string $auctionId
     * @param string $bidId
     * @param string $cost
     * @param string $xml
     * @param string $hash
     * @param string $sign
     * @param string $clientIp
     */
    public function __construct(
        string $profileId,
        string $auctionId,
        string $bidId,
        string $cost,
        string $xml,
        string $hash,
        string $sign,
        string $clientIp
    ) {
        $this->profileId = $profileId;
        $this->auctionId = $auctionId;
        $this->bidId = $bidId;
        $this->cost = $cost;
        $this->xml = $xml;
        $this->hash = $hash;
        $this->sign = $sign;
        $this->clientIp = $clientIp;
    }
}