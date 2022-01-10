<?php


namespace App\Model\Work\Procedure\Entity;


use Webmozart\Assert\Assert;

class ConductingType
{
    public const OPEN = 'OPEN';
    public const CLOSED = 'CLOSED';

    private static $names = [
        self::OPEN => 'Открытый',
        self::CLOSED => 'Закрытый'
    ];

    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::OPEN,
            self::CLOSED
        ]);
        $this->value = $value;
    }

    public static function open(): self
    {
        return new self(self::OPEN);
    }

    public static function closed(): self
    {
        return new self(self::CLOSED);
    }

    public function isOpen(): bool
    {
        return $this->value === self::OPEN;
    }

    public function isClosed(): bool
    {
        return $this->value === self::CLOSED;
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