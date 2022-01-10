<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile;

use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Model\Profile\Entity\Profile
 */
class Status
{
    private const STATUS_ACTIVE = 'STATUS_ACTIVE';
    private const STATUS_DRAFT = 'STATUS_DRAFT';
    private const STATUS_WAIT = 'STATUS_WAIT';
    private const STATUS_MODERATE = 'STATUS_MODERATE';
    private const STATUS_ARCHIVED = 'STATUS_ARCHIVE';
    private const STATUS_REJECTED = 'STATUS_REJECT';
    private const STATUS_BLOCKED = 'STATUS_BLOCK';
    private const STATUS_REPLACING_EP = 'STATUS_REPLACING_EP';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_ACTIVE,
            self::STATUS_DRAFT,
            self::STATUS_WAIT,
            self::STATUS_MODERATE,
            self::STATUS_ARCHIVED,
            self::STATUS_REJECTED,
            self::STATUS_BLOCKED,
            self::STATUS_REPLACING_EP,
        ]);
        $this->name = $name;
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
    public static function draft(): self {
        return new self(self::STATUS_DRAFT);
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
    public static function moderate(): self {
        return new self(self::STATUS_MODERATE);
    }

    /**
     * @return Status
     */
    public static function archived(): self {
        return new self(self::STATUS_ARCHIVED);
    }

    /**
     * @return Status
     */
    public static function rejected(): self {
        return new self(self::STATUS_REJECTED);
    }

    /**
     * @return Status
     */
    public static function blocked(): self {
        return new self(self::STATUS_BLOCKED);
    }

    /**
     * @return Status
     */
    public static function replacingEp(): self {
        return new self(self::STATUS_REPLACING_EP);
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
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
    public function isDraft(): bool {
        return $this->name === self::STATUS_DRAFT;
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
    public function isModerate(): bool {
        return $this->name === self::STATUS_MODERATE;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool {
        return $this->name === self::STATUS_ARCHIVED;
    }

    /**
     * @return bool
     */
    public function isRejected(): bool {
        return $this->name === self::STATUS_REJECTED;
    }

    /**
     * @return bool
     */
    public function isBlocked(): bool {
        return $this->name === self::STATUS_BLOCKED;
    }

    /**
     * @return bool
     */
    public function isReplacingEp(): bool {
        return $this->name === self::STATUS_REPLACING_EP;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

}