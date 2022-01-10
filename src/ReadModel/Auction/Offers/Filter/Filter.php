<?php
declare(strict_types=1);
namespace App\ReadModel\Auction\Offers\Filter;

class Filter
{
    public $auction_id;

    public function __construct(? string $auction_id) {
        $this->auction_id = $auction_id;
    }

    public static function forAuctionId(string $auction_id): self{
        return new self($auction_id);
    }

}