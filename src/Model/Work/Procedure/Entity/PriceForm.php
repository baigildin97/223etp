<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use Webmozart\Assert\Assert;

class PriceForm
{
    public const OPEN = 'OPEN';
    public const CLOSE = 'CLOSE';

    private $name;

    private static $names = [
      self::OPEN => 'Открытая',
      self::CLOSE => 'Закрытая'
    ];

    public function __construct(string $name){
        Assert::oneOf($name,[
            self::OPEN,
            self::CLOSE
        ]);
        $this->name = $name;
    }

    public static function open(): self {
        return new self(self::OPEN);
    }

    public static function close(): self{
        return new self(self::CLOSE);
    }

    public function isOpen(): bool {
        return $this->name === self::OPEN;
    }

    public function isClose(): bool {
        return $this->name === self::CLOSE;
    }

    public function isEqual(self $status): bool {
        return $this->getName() === $status->getName();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLocalizedName(): string {
        return self::$names[$this->name];
    }
}
