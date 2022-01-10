<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Document;

use Webmozart\Assert\Assert;


class Status
{
    private const STATUS_NEW = 'STATUS_NEW';
    private const STATUS_SIGNED = 'STATUS_SIGNED';
    private const STATUS_DELETED = 'STATUS_DELETED';
    private const STATUS_UPDATE = 'STATUS_UPDATE';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::STATUS_NEW,
            self::STATUS_SIGNED,
            self::STATUS_DELETED
        ]);
        $this->name = $name;
    }

    public static function new(): self
    {
        return new self(self::STATUS_NEW);
    }

    public static function signed(): self
    {
        return new self(self::STATUS_SIGNED);
    }

    public static function deleted(): self
    {
        return new self(self::STATUS_DELETED);
    }

    public function isNew(): bool
    {
        return $this->name === self::STATUS_NEW;
    }

    public function isSigned(): bool
    {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isDeleted(): bool
    {
        return $this->name === self::STATUS_DELETED;
    }

    public function getName(): string
    {
        return $this->name;
    }
}