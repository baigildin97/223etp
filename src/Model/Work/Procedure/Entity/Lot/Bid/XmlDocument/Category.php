<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument;


use Webmozart\Assert\Assert;

class Category
{
    public const SENT = 'SENT';
    public const RECALL = 'RECALL';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::SENT,
            self::RECALL
        ]);
        $this->name = $name;
    }

    public static function sent(): self {
        return new self(self::SENT);
    }

    public static function recall(): self {
        return new self(self::RECALL);
    }

    public function isSent(): bool {
        return $this->name === self::SENT;
    }

    public function isRecall(): bool {
        return $this->name === self::RECALL;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}