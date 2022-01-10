<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use Webmozart\Assert\Assert;

class Status
{
    public const NEW = 'STATUS_NEW';
    public const SENT = 'STATUS_SENT';
    public const RECALLED = 'STATUS_RECALLED';
    public const REJECT = 'STATUS_REJECT';
    public const APPROVED = 'STATUS_APPROVED';

    private $name;

    private static $names = [
        self::NEW => 'Черновик',
        self::SENT => 'Заявка отправлена',
        self::RECALLED => 'Заявка отозвана',
        self::REJECT => 'Заявка отклонена',
        self::APPROVED => 'Заявка допущена'
    ];

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::SENT,
            self::RECALLED,
            self::REJECT,
            self::NEW,
            self::APPROVED
        ]);
        $this->name = $name;
    }

    public static function sent(): self {
        return new self(self::SENT);
    }

    public static function recalled(): self {
        return new self(self::RECALLED);
    }

    public static function reject(): self{
        return new self(self::REJECT);
    }

    public static function new(): self {
        return new self(self::NEW);
    }

    public static function approved(): self {
        return new self(self::APPROVED);
    }

    public function isRecalled(): bool {
        return $this->name === self::RECALLED;
    }

    public function isSent(): bool {
        return $this->name === self::SENT;
    }

    public function isReject(): bool {
        return $this->name === self::REJECT;
    }

    public function isNew(): bool {
        return $this->name === self::NEW;
    }

    public function isApproved(): bool {
        return $this->name === self::APPROVED;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string {
        return self::$names[$this->name];
    }
}