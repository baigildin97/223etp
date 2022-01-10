<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Auction;

use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_WAIT = 'STATUS_WAIT';
    public const STATUS_ACTIVE = 'STATUS_ACTIVE';
    public const STATUS_COMPLETED = 'STATUS_COMPLETED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::STATUS_WAIT,
            self::STATUS_ACTIVE,
            self::STATUS_COMPLETED
        ]);
        $this->name = $name;
    }

    public static function wait(): self
    {
        return new self(self::STATUS_WAIT);
    }

    public static function active(): self
    {
        return new self(self::STATUS_ACTIVE);
    }

    public static function completed(): self
    {
        return new self(self::STATUS_COMPLETED);
    }

    public function isWait(): bool
    {
        return $this->name === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->name === self::STATUS_ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->name === self::STATUS_COMPLETED;
    }

    public function isEqual(self $status): bool
    {
        return $this->getName() === $status->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}