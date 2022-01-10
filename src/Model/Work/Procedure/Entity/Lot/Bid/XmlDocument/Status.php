<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument;


use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_NOT_SIGNED = 'STATUS_NOT_SIGNED';
    public const STATUS_SIGNED = 'STATUS_SIGNED';
    public const STATUS_REJECT = 'STATUS_REJECT';
    public const STATUS_APPROVED = 'STATUS_APPROVED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::STATUS_NOT_SIGNED,
            self::STATUS_SIGNED,
            self::STATUS_REJECT,
            self::STATUS_APPROVED
        ]);
        $this->name = $name;
    }

    public static function notSigned(): self {
        return new self(self::STATUS_NOT_SIGNED);
    }

    public static function signed(): self {
        return new self(self::STATUS_SIGNED);
    }

    public static function reject(): self {
        return new self(self::STATUS_REJECT);
    }

    public static function approved(): self {
        return new self(self::STATUS_APPROVED);
    }


    public function isNotSigned(): bool {
        return $this->name === self::STATUS_NOT_SIGNED;
    }

    public function isSigned(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isReject(): bool {
        return $this->name === self::STATUS_REJECT;
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