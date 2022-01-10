<?php
declare(strict_types=1);
namespace App\Model\User\Service;


use App\Model\User\Entity\User\NewEmailResetToken;

class NewEmailTokenGenerator
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): NewEmailResetToken {
        return new NewEmailResetToken(
            (new TokenGenerator())->generate(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}