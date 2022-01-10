<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Confirm\ByToken;


class Command
{
    public $confirmToken;

    public function __construct(string $confirmToken)
    {
        $this->confirmToken = $confirmToken;
    }
}