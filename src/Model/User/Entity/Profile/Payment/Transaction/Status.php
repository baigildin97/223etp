<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;


use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_COMPLETED = 'STATUS_COMPLETED';
    public const STATUS_PENDING = 'STATUS_PENDING';
    public const STATUS_ARCHIVED = 'STATUS_ARCHIVE';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_COMPLETED,
            self::STATUS_PENDING,
            self::STATUS_ARCHIVED
        ]);
        $this->name = $name;
    }

    /**
     * @return Status
     */
    public static function pending(): self {
        return new self(self::STATUS_PENDING);
    }

    /**
     * @return Status
     */
    public static function completed(): self {
        return new self(self::STATUS_COMPLETED);
    }

    /**
     * @return Status
     */
    public static function archived(): self {
        return new self(self::STATUS_ARCHIVED);
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
    public function isCompleted(): bool {
        return $this->name === self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isPending(): bool {
        return $this->name === self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool {
        return $this->name === self::STATUS_ARCHIVED;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
