<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\XmlDocument\Filter;


class Filter
{

    public $bidId;

    public function __construct(? string $bidId) {
        $this->bidId = $bidId;
    }

    public static function fromBid(string $bidId): self {
        return new self($bidId);
    }
}