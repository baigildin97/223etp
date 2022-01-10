<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Auction\Close;


class Command
{

    public $auctionId;

    public function __construct(string $auctionId)
    {
        $this->auctionId = $auctionId;
    }

}