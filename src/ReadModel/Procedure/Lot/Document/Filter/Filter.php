<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Document\Filter;


class Filter
{
    /**
     * @var string|null
     */
    public $lotId;

    /**
     * Filter constructor.
     * @param string|null $lotId
     */
    public function __construct(? string $lotId) {
        $this->lotId = $lotId;
    }

    /**
     * @param string $lotId
     * @return static
     */
    public static function fromLot(string $lotId): self{
        return new self($lotId);
    }
}