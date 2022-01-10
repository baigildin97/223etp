<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;


use Webmozart\Assert\Assert;

class Status
{
    public const STATUS_SIGNED = 'STATUS_SIGNED';
    public const STATUS_NEW = 'STATUS_NEW';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::STATUS_NEW,
            self::STATUS_SIGNED
        ]);
        $this->name = $name;
    }

    public static function new(): self {
        return new self(self::STATUS_NEW);
    }

    public static function signed(): self{
        return new self(self::STATUS_SIGNED);
    }

    public function isNew(): bool {
        return $this->name === self::STATUS_NEW;
    }

    public function isSigned(): bool {
        return $this->name === self::STATUS_SIGNED;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}