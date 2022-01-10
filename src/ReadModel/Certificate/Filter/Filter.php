<?php
declare(strict_types=1);
namespace App\ReadModel\Certificate\Filter;


class Filter
{
    public $user_id;
    public $id;
    public $thumbprint;
    public $subject_name_owner;

    private function __construct(? string $user_id)
    {
        $this->user_id = $user_id;
    }

    public static function forUserId(string $user_id): self
    {
        return new self($user_id);
    }
}