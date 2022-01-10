<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Tasks;


use Webmozart\Assert\Assert;

class Category
{
    private const STATUS_NEW = 'STATUS_NEW';
    private const STATUS_ACTIVE = 'STATUS_ACTIVE';
    private const STATUS_COMPLETED = 'STATUS_COMPLETED';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name) {
        Assert::oneOf($name,[
            self::STATUS_NEW,
            self::STATUS_ACTIVE,
            self::STATUS_COMPLETED
        ]);
        $this->name = $name;
    }

    public static function new(): self{
        return new self(self::STATUS_NEW);
    }

    /**
     * @return Category
     */
    public static function active(): self {
        return new self(self::STATUS_ACTIVE);
    }

    /**
     * @return Category
     */
    public static function completed(): self {
        return new self(self::STATUS_COMPLETED);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}
