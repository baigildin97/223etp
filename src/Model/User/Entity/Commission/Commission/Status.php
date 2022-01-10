<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Commission;

use Webmozart\Assert\Assert;

class Status
{
    public const ACTIVE = 'STATUS_ACTIVE';
    public const ARCHIVE = 'STATUS_ARCHIVED';
    public const DRAFT = 'STATUS_DRAFT';

    private $value;

    public function __construct(string $value)
    {
        Assert::oneOf($value,[
            self::ACTIVE,
            self::ARCHIVE,
            self::DRAFT
        ]);
        $this->value = $value;
    }

    public static function active(): self {
        return new self(self::ACTIVE);
    }

    public static function archived(): self{
        return new self(self::ARCHIVE);
    }

    public static function draft(): self{
        return new self(self::DRAFT);
    }

    public function isActive(): bool {
        return $this->value === self::ACTIVE;
    }

    public function isArchive(): bool {
        return $this->value === self::ARCHIVE;
    }

    public function isDraft(): bool {
        return $this->value === self::DRAFT;
    }

    public function isEqual(self $status): bool {
        return $this->getValue() === $status->getValue();
    }

    public function getValue(): string {
        return $this->value;
    }
}