<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Archived;


class Command
{

    public $memberId;

    public function __construct(string $memberId) {
        $this->memberId = $memberId;
    }

}