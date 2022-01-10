<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Lot;

use App\ReadModel\Procedure\Lot\LotFetcher;

/**
 * Class NumberGenerator
 * @package App\Model\Work\Procedure\Services\Procedure\Lot
 */
class NumberGenerator
{
    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * NumberGenerator constructor.
     * @param LotFetcher $lotFetcher
     */
    public function __construct(LotFetcher $lotFetcher){
        $this->lotFetcher = $lotFetcher;
    }

    /**
     * @return int
     */
    public function next(): int{
     return $this->lotFetcher->findLastIdNumber() + 1;
    }

}