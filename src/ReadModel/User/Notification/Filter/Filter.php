<?php
declare(strict_types=1);
namespace App\ReadModel\User\Notification\Filter;


use App\Model\User\Entity\User\Notification\Category;

class Filter
{
    public $text;
    public $user_id;
    public $category;

    public function __construct(? string $user_id, ? string $category) {
        $this->user_id = $user_id;
        $this->category = $category;
    }

    public static function forUserId(string $user_id): self{
        return new self($user_id, null);
    }

    public static function forCategory(string $category): self{
        return new self(null, $category);
    }
}