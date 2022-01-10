<?php
declare(strict_types=1);
namespace App\ReadModel\Auction\Filter;


use App\Model\Work\Procedure\Entity\Lot\Auction\Status;

class Filter
{
    public $winner_id;
    public $id_number;
    public $statusWait;

    public function __construct(? string $winner_id) {
        $this->winner_id = $winner_id;
    }

    public static function forWinnerId(string $winner_id): self{
        return new self($winner_id);
    }

    public static function forStatusWait(Status $status): self{
        return new self($status->getName());
    }
}