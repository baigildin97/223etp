<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User\Notification;


use Webmozart\Assert\Assert;

class Category
{
    //Уведомления для админов и модераторв
    private const CATEGORY_ONE = 'CATEGORY_ONE';
    private const CATEGORY_ADMIN = 'CATEGORY_ADMIN';

    private $name;

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::CATEGORY_ONE,
            self::CATEGORY_ADMIN
        ]);
        $this->name = $name;
    }

    public static function categoryOne(): self
    {
        return new self(self::CATEGORY_ONE);
    }

    public static function categoryAdmin(): self
    {
        return new self(self::CATEGORY_ADMIN);
    }

    /**
     * @return bool
     */
    public function isCategoryOne(): bool{
        return $this->name === self::CATEGORY_ONE;
    }

    /**
     * @return bool
     */
    public function isCategoryAdmin(): bool{
        return $this->name === self::CATEGORY_ADMIN;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
