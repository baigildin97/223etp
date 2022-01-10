<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Document;

use Webmozart\Assert\Assert;

/**
 * Class Status
 * @package App\Model\Profile\Entity\Profile\Document
 */
class Status
{
    private const STATUS_NEW = 'STATUS_NEW';
    private const STATUS_SIGNED = 'STATUS_SIGNED';
    private const STATUS_DELETED = 'STATUS_DELETED';

    private $name;

    private static $names = [
        self::STATUS_NEW => 'Новый',
        self::STATUS_SIGNED => 'Подписан',
        self::STATUS_DELETED => 'Удален'
    ];

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_NEW,
            self::STATUS_SIGNED,
            self::STATUS_DELETED
        ]);
        $this->name = $name;
    }

    /**
     * @return Status
     */
    public static function new(): self {
        return new self(self::STATUS_NEW);
    }

    /**
     * @return Status
     */
    public static function signed(): self {
        return new self(self::STATUS_SIGNED);
    }

    /**
     * @return Status
     */
    public static function deleted(): self {
        return new self(self::STATUS_DELETED);
    }

    /**
     * @return bool
     */
    public function isNEW(): bool {
        return $this->name === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isSigned(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool {
        return $this->name === self::STATUS_DELETED;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string{
        return self::$names[$this->name];
    }

}