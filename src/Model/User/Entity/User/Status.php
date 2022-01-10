<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;


use Webmozart\Assert\Assert;

class Status
{
    private const STATUS_NEW = 'new';
    private const STATUS_WAIT = 'wait';
    private const STATUS_ACTIVE = 'active';
    private const STATUS_BLOCKED = 'blocked';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_NEW,
            self::STATUS_WAIT,
            self::STATUS_ACTIVE,
            self::STATUS_BLOCKED
        ]);
        $this->name = $name;
    }

    public static function new(): self{
        return new self(self::STATUS_NEW);
    }

    /**
     * @return Status
     */
    public static function active(): self {
        return new self(self::STATUS_ACTIVE);
    }

    /**
     * @return Status
     */
    public static function wait(): self {
        return new self(self::STATUS_WAIT);
    }

    /**
     * @return Status
     */
    public static function blocked(): self {
        return new self(self::STATUS_BLOCKED);
    }

    /**
     * @param self
     * @return bool
     */
    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function isNew(): bool {
        return $this->name === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->name === self::STATUS_ACTIVE;
    }


    /**
     * @return bool
     */
    public function isWait(): bool {
        return $this->name === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool {
        return $this->name === self::STATUS_BLOCKED;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
