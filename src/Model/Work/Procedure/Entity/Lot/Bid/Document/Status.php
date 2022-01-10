<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\Document;


use Webmozart\Assert\Assert;

class Status
{
    public const NEW = 'STATUS_NEW';
    public const SIGNED = 'STATUS_SIGNED';
    public const ARCHIVED = 'STATUS_ARCHIVED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::NEW,
            self::SIGNED,
            self::ARCHIVED
        ]);
        $this->name = $name;
    }

    public static function new(): self{
        return new self(self::NEW);
    }

    public static function signed(): self{
        return new self(self::SIGNED);
    }

    public static function archived(): self {
        return new self(self::ARCHIVED);
    }

    public function isSigned(): bool {
        return $this->name === self::SIGNED;
    }

    public function isArchived(): bool {
        return $this->name === self::ARCHIVED;
    }

    public function isNew(): bool {
        return $this->name === self::NEW;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}