<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid;



use App\ReadModel\Procedure\Bid\BidFetcher;

class NumberGenerator
{
    private $bidFetcher;

    public function __construct(BidFetcher $bidFetcher){
        $this->bidFetcher = $bidFetcher;
    }

    public function next(): int{
        return $this->bidFetcher->findLastIdNumber() + 1;
    }

}