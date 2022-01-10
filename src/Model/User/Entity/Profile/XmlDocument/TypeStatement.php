<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\XmlDocument;


use Webmozart\Assert\Assert;

class TypeStatement
{
    public const TYPE_REGISTRATION = 'TYPE_REGISTRATION';
    public const TYPE_EDIT = 'TYPE_EDIT';
    public const TYPE_RECALL = 'TYPE_RECALL';
    public const TYPE_REPLACING_EP = 'TYPE_REPLACING_EP';

    private $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::TYPE_REGISTRATION,
            self::TYPE_EDIT,
            self::TYPE_RECALL,
            self::TYPE_REPLACING_EP
        ]);
        $this->name = $name;
    }

    public static function registration(): self {
        return new self(self::TYPE_REGISTRATION);
    }

    public static function edit(): self {
        return new self(self::TYPE_EDIT);
    }

    public static function recall(): self {
        return new self(self::TYPE_RECALL);
    }

    public static function replacingEp(): self {
        return new self(self::TYPE_REPLACING_EP);
    }

    public function isRegistration(): bool {
        return $this->name === self::TYPE_REGISTRATION;
    }

    public function isRecall(): bool {
        return $this->name === self::TYPE_RECALL;
    }

    public function isEdit(): bool {
        return $this->name === self::TYPE_EDIT;
    }

    public function isReplacingEp(): bool {
        return $this->name === self::TYPE_REPLACING_EP;
    }


    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }
}