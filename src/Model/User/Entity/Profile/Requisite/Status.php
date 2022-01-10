<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Requisite;

use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Model\Profile\Entity\Profile
 */
class Status
{
    private const STATUS_ACTIVE = 'STATUS_ACTIVE';
    private const STATUS_INACTIVE = 'STATUS_INACTIVE';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE
        ]);
        $this->name = $name;
    }

    /**
     * @return Status
     */
    public static function active(): self {
        return new self(self::STATUS_ACTIVE);
    }

    public static function inactive(): self {
        return new self(self::STATUS_INACTIVE);
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
    public function isInactive(): bool {
        return $this->name === self::STATUS_INACTIVE;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

}