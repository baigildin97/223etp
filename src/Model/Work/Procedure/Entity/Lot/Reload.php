<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;


use Webmozart\Assert\Assert;

class Reload
{
    private const NO = 'NO';
    private const YES = 'YES';

    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value,[
            self::NO,
            self::YES
        ]);
        $this->value = $value;
    }

    public static function no(): self {
        return new self(self::NO);
    }

    public static function yes(): self{
        return new self(self::YES);
    }

    public function isNo(): bool {
        return $this->value === self::NO;
    }

    public function isYes(): bool {
        return $this->value === self::YES;
    }

    public function isEqual(self $value): bool {
        return $this->getValue() === $value->getValue();
    }

    public function getValue(): string {
        return $this->value;
    }
}