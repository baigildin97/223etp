<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Protocol\Filter;


class Filter
{
    public $procedureId;

    public function __construct(string $procedureId) {
        $this->procedureId = $procedureId;
    }

    /**
     * @param string $procedureId
     * @return static
     */
    public static function fromProcedure(string $procedureId): self{
        return new self($procedureId);
    }
}