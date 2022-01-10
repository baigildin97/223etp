<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bidId;

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
    public $auctionId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xml;

    public function __construct(string $bidId, string $xml, string $hash, string $auctionId){
        $this->bidId = $bidId;
        $this->xml = $xml;
        $this->hash = $hash;
        $this->auctionId = $auctionId;
    }
}