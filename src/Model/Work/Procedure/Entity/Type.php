<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Webmozart\Assert\Assert;

/**
 * Class Role
 * @package App\Model\Procedure\Entity
 */
class Type
{
    public const REQUEST_OFFERS = 'REQUEST_OFFERS';
    public const CONTEST = 'CONTEST';
    public const AUCTION = 'AUCTION';

    private $value;

    private static $names = [
        self::REQUEST_OFFERS => 'Публичное предложение',
        self::CONTEST => 'Конкурс',
        self::AUCTION => 'Аукцион'
    ];

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::REQUEST_OFFERS,
            self::CONTEST,
            self::AUCTION
        ]);
        $this->value = $value;
    }

    public static function requestOffers(): self {
        return new self(self::REQUEST_OFFERS);
    }

    public static function auction(): self {
        return new self(self::AUCTION);
    }

    public static function contest(): self{
        return new self(self::CONTEST);
    }

    public function isRequestOffers(): bool {
        return $this->value === self::REQUEST_OFFERS;
    }

    public function isContest(): bool {
        return $this->value === self::CONTEST;
    }

    public function isAuction(): bool {
        return $this->value === self::AUCTION;
    }

    public function isEqual(self $status): bool {
        return $this->getValue() === $status->getValue();
    }

    public function getLocalizedName(): string {
        return self::$names[$this->value];
    }

    public function getValue(): string {
        return $this->value;
    }


}