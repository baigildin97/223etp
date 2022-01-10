<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Filter;


class Filter
{
    public $status;

    public function __construct(? string $status)
    {
        $this->status = $status;
    }

    public static function fromStatus(string $status): self {
        return new self($status);
    }

}