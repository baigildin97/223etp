<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Document\Filter;


class Filter
{
    /**
     * @var string|null
     */
    public $profileId;

    /**
     * Filter constructor.
     * @param string|null $profileId
     */
    public function __construct(? string $profileId) {
        $this->profileId = $profileId;
    }

    /**
     * @param string $profileId
     * @return static
     */
    public static function fromProfile(string $profileId): self{
        return new self($profileId);
    }
}