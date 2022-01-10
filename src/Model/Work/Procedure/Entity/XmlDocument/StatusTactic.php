<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;


use Webmozart\Assert\Assert;

class StatusTactic
{
    public const STATUS_PUBLISHED = 'STATUS_PUBLISHED';
    public const STATUS_RECALLED = 'STATUS_RECALLED';
    public const STATUS_PROCESSING = 'STATUS_PROCESSING';
    public const STATUS_REJECTED = 'STATUS_REJECTED';
    public const STATUS_APPROVED = 'STATUS_APPROVED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::STATUS_PUBLISHED,
            self::STATUS_RECALLED,
            self::STATUS_APPROVED,
            self::STATUS_PROCESSING,
            self::STATUS_REJECTED
        ]);
        $this->name = $name;
    }

    public static function published(): self {
        return new self(self::STATUS_PUBLISHED);
    }

    public static function processing(): self {
        return new self(self::STATUS_PROCESSING);
    }
    public static function recalled(): self {
        return new self(self::STATUS_RECALLED);
    }

    public static function approved(): self {
        return new self(self::STATUS_APPROVED);
    }

    public static function rejected(): self{
        return new self(self::STATUS_REJECTED);
    }

    public function isPublished(): bool {
        return $this->name === self::STATUS_PUBLISHED;
    }

    public function isNotPublished(): bool {
        return $this->name === self::STATUS_PUBLISHED;
    }

    public function isProcessing(): bool {
        return $this->name === self::STATUS_PROCESSING;
    }

    public function isRecalled(): bool {
        return $this->name === self::STATUS_RECALLED;
    }

    public function isRejected(): bool {
        return $this->name === self::STATUS_REJECTED;
    }

    public function isApproved(): bool {
        return $this->name === self::STATUS_APPROVED;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}