<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Certificate\Reset\Confirm;


class Command
{
    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}