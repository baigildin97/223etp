<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment\Filter;


class Filter
{
    public $id;

    public $profileId;

    public function __construct(? string $profileId) {
        $this->profileId = $profileId;
    }

    public static function fromProfile(string $profileId): self{
        return new self($profileId);
    }
}