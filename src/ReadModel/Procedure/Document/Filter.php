<?php
namespace App\ReadModel\Procedure\Document;


class Filter
{

    public $procedureId;

    /**
     * Filter constructor.
     * @param string|null $procedureId
     */
    public function __construct(? string $procedureId) {
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