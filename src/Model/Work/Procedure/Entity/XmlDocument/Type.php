<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;


use Webmozart\Assert\Assert;

class Type
{
    public const TYPE_NOTIFY_DOCUMENT = 'TYPE_NOTIFY_DOCUMENT';
    public const TYPE_NOTIFY_PAUSE = 'TYPE_NOTIFY_PAUSE';
    public const TYPE_NOTIFY_CANCEL = 'TYPE_NOTIFY_CANCEL';
    public const TYPE_NOTIFY_RESUME = 'TYPE_NOTIFY_RESUME';
    public const TYPE_DEFAULT_DOCUMENT = 'TYPE_DEFAULT_DOCUMENT';

    private $name;

    public static $names = [
        self::TYPE_DEFAULT_DOCUMENT => '',
        self::TYPE_NOTIFY_DOCUMENT => 'Извещение о проведении торговой процедуры',
       // self::TYPE_NOTIFY_PAUSE => 'Извещение о приостановлении торговой процедуры',
       // self::TYPE_NOTIFY_RESUME => 'Извещение о возобновлении торговой процедуры',
        self::TYPE_NOTIFY_CANCEL => 'Извещение об отмене торговой процедуры'
    ];

    public function __construct(string $name)
    {
        Assert::oneOf($name,[
            self::TYPE_NOTIFY_DOCUMENT,
            self::TYPE_DEFAULT_DOCUMENT,
            self::TYPE_NOTIFY_PAUSE,
            self::TYPE_NOTIFY_RESUME,
            self::TYPE_NOTIFY_CANCEL
        ]);
        $this->name = $name;
    }

    public static function notifyPublish(): self {
        return new self(self::TYPE_NOTIFY_DOCUMENT);
    }

    public static function notifyCancel(): self{
        return new self(self::TYPE_NOTIFY_CANCEL);
    }

    public static function notifyPause(): self{
        return new self(self::TYPE_NOTIFY_PAUSE);
    }

    public static function notifyResume(): self{
        return new self(self::TYPE_NOTIFY_RESUME);
    }

    public static function default(): self {
        return new self(self::TYPE_DEFAULT_DOCUMENT);
    }

    public function isNotifyPublish(): bool {
        return $this->name === self::TYPE_NOTIFY_DOCUMENT;
    }

    public function isNotifyCancel(): bool{
        return $this->name === self::TYPE_NOTIFY_CANCEL;
    }

    public function isNotifyPause(): bool{
        return $this->name === self::TYPE_NOTIFY_PAUSE;
    }

    public function isNotifyResume(): bool{
        return $this->name === self::TYPE_NOTIFY_RESUME;
    }

    public function isDefault(): bool {
        return $this->name === self::TYPE_DEFAULT_DOCUMENT;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string{
        return self::$names[$this->name];
    }
}