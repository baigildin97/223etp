<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;

use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_NOT_SIGNED = 'STATUS_NOT_SIGNED';
    public const STATUS_SIGNED = 'STATUS_SIGNED';
    public const STATUS_MODERATE = 'STATUS_MODERATE';
    public const STATUS_APPROVE = 'STATUS_APPROVE';
    public const STATUS_NOT_ACTIVE = 'STATUS_NOT_ACTIVE';
    public const STATUS_REJECTED = 'STATUS_REJECTED';
    public const STATUS_RECALLED = 'STATUS_RECALLED';
    public const STATUS_CANCELLING_PUBLICATION = 'STATUS_CANCELLING_PUBLICATION';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::STATUS_NOT_SIGNED,
            self::STATUS_SIGNED,
            self::STATUS_APPROVE,
            self::STATUS_NOT_ACTIVE,
            self::STATUS_REJECTED,
            self::STATUS_MODERATE,
            self::STATUS_RECALLED,
            self::STATUS_CANCELLING_PUBLICATION
        ]);
        $this->name = $name;
    }

    public static function notSigned(): self {
        return new self(self::STATUS_NOT_SIGNED);
    }

    public static function signed(): self {
        return new self(self::STATUS_SIGNED);
    }

    public static function recalled(): self{
        return new self(self::STATUS_RECALLED);
    }

    public static function approve(): self {
        return new self(self::STATUS_APPROVE);
    }

    public static function rejected(): self {
        return new self(self::STATUS_REJECTED);
    }

    public static function cancellingPublication(): self {
        return new self(self::STATUS_CANCELLING_PUBLICATION);
    }

    public static function notActive(): self {
        return new self(self::STATUS_NOT_ACTIVE);
    }

    public static function moderate(): self{
        return new self(self::STATUS_MODERATE);
    }

    public function isModerate(): bool{
        return $this->name === self::STATUS_MODERATE;
    }

    public function isNotSigned(): bool {
        return $this->name === self::STATUS_NOT_SIGNED;
    }

    public function isSigned(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isActive(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isRejected(): bool{
        return $this->name === self::STATUS_REJECTED;
    }

    public function isApproved(): bool{
        return $this->name === self::STATUS_APPROVE;
    }

    public function isNotActive(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isRecalled(): bool {
        return $this->name === self::STATUS_RECALLED;
    }


    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}