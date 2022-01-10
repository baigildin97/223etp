<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument\History;


use Webmozart\Assert\Assert;

class Action
{
    public const ACTION_SEND = 'ACTION_SEND';
    public const ACTION_PROCESSING = 'ACTION_PROCESSING';
    public const ACTION_APPROVED = 'ACTION_APPROVED';
    public const ACTION_REJECTED = 'ACTION_REJECTED';
    public const ACTION_RETURNED = 'ACTION_RETURNED';
    public const ACTION_RECALLED = 'ACTION_RECALLED';
    public const ACTION_CANCEL_PUBLISHED = 'ACTION_CANCEL_PUBLISHED';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::ACTION_SEND,
            self::ACTION_PROCESSING,
            self::ACTION_APPROVED,
            self::ACTION_REJECTED,
            self::ACTION_RETURNED,
            self::ACTION_RECALLED,
            self::ACTION_CANCEL_PUBLISHED
        ]);
        $this->name = $name;
    }

    public static function send(): self {
        return new self(self::ACTION_SEND);
    }

    public static function processing(): self {
        return new self(self::ACTION_PROCESSING);
    }

    public static function approved(): self {
        return new self(self::ACTION_APPROVED);
    }

    public static function rejected(): self {
        return new self(self::ACTION_REJECTED);
    }

    public static function returned(): self {
        return new self(self::ACTION_RETURNED);
    }

    public static function recalled(): self {
        return new self(self::ACTION_RECALLED);
    }

    public static function cancelPublished(): self {
        return new self(self::ACTION_CANCEL_PUBLISHED);
    }

    public function isRecalled(): bool {
        return $this->name === self::ACTION_RECALLED;
    }

    public function isSend(): bool {
        return $this->name === self::ACTION_SEND;
    }

    public function isProcessing(): bool {
        return $this->name === self::ACTION_PROCESSING;
    }

    public function isApproved(): bool {
        return $this->name === self::ACTION_APPROVED;
    }

    public function isRejected(): bool {
        return $this->name === self::ACTION_REJECTED;
    }

    public function isReturned(): bool {
        return $this->name === self::ACTION_RETURNED;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}