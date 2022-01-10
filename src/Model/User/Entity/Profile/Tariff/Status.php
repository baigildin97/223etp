<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use Webmozart\Assert\Assert;

class Status
{
    private const STATUS_ACTIVE = 'STATUS_ACTIVE';
    private const STATUS_DRAFT = 'STATUS_DRAFT';
    private const STATUS_ARCHIVED = 'STATUS_ARCHIVE';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_ACTIVE,
            self::STATUS_DRAFT,
            self::STATUS_ARCHIVED
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
    public static function archived(): self {
        return new self(self::STATUS_ARCHIVED);
    }

    /**
     * @param self
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