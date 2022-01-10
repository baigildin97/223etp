<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\Document\Filter;


class Filter
{
    /**
     * @var string|null
     */
    public $bidId;

    /**
     * Filter constructor.
     * @param string|null $bidId
     */
    public function __construct(? string $bidId) {
        $this->bidId = $bidId;
    }

    /**
     * @param string $bidId
     * @return static
     */
    public static function fromBid(string $bidId): self{
        return new self($bidId);
    }
}