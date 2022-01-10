<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use Webmozart\Assert\Assert;

class TempStatus
{
    public const REJECT = 'STATUS_REJECT';
    public const APPROVED = 'STATUS_APPROVED';
    public const NEW = 'STATUS_NEW';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::REJECT,
            self::APPROVED,
            self::NEW
        ]);
        $this->name = $name;
    }

    public static function reject(): self{
        return new self(self::REJECT);
    }

    public static function approved(): self {
        return new self(self::APPROVED);
    }

    public static function new(): self {
        return new self(self::NEW);
    }


    public function isReject(): bool {
        return $this->name === self::REJECT;
    }

    public function isApproved(): bool {
        return $this->name === self::APPROVED;
    }

    public function isNew(): bool {
        return $this->name === self::new();
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}