<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Webmozart\Assert\Assert;

class Contract
{
    public const ON_SITE = 'ON_SITE';
    public const ON_PAPER = 'ON_PAPER';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::ON_SITE,
            self::ON_PAPER,
        ]);
        $this->name = $name;
    }

    public static function onSite(): self {
        return new self(self::ON_SITE);
    }

    public static function onPaper(): self{
        return new self(self::ON_PAPER);
    }

    public function isOnSite(): bool {
        return $this->name === self::ON_PAPER;
    }

    public function isOnPaper(): bool {
        return $this->name === self::ON_PAPER;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}