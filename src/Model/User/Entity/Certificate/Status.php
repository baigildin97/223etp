<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Certificate;


use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Model\User\Entity\Certificate
 */
class Status
{
    public const ACTIVE = 'STATUS_ACTIVE';
    public const MODERATE = 'STATUS_MODERATE';
    public const ARCHIVE = 'STATUS_ARCHIVED';
    public const REJECT = 'STATUS_REJECT';
    public const WAIT = 'STATUS_WAIT';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::ACTIVE,
            self::MODERATE,
            self::ARCHIVE,
            self::REJECT,
            self::WAIT
        ]);
        $this->name = $name;
    }

    public static function active(): self {
        return new self(self::ACTIVE);
    }

    public static function moderate(): self{
        return new self(self::MODERATE);
    }

    public static function archived(): self{
        return new self(self::ARCHIVE);
    }

    public static function reject(): self{
        return new self(self::REJECT);
    }

    public static function wait(): self{
        return new self(self::WAIT);
    }

    public function isActive(): bool {
        return $this->name === self::ACTIVE;
    }

    public function isModerate(): bool {
        return $this->name === self::MODERATE;
    }


    public function isArchive(): bool {
        return $this->name === self::ARCHIVE;
    }

    public function isReject(): bool {
        return $this->name === self::REJECT;
    }

    public function isWait(): bool {
        return $this->name === self::WAIT;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}