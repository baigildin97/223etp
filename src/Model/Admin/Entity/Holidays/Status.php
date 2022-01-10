<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Holidays;

use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Model\Admin\Entity\Holidays
 */
class Status
{
    public const ACTIVE = 'STATUS_ACTIVE';
    public const ARCHIVE = 'STATUS_ARCHIVED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::ACTIVE,
            self::ARCHIVE
        ]);
        $this->name = $name;
    }

    public static function active(): self {
        return new self(self::ACTIVE);
    }

    public static function archived(): self{
        return new self(self::ARCHIVE);
    }


    public function isActive(): bool {
        return $this->name === self::ACTIVE;
    }

    public function isArchive(): bool {
        return $this->name === self::ARCHIVE;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}