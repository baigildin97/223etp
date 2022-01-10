<?php
declare(strict_types=1);
namespace App\Model\User\Service;


use App\Model\User\Entity\User\ResetToken;

class ResetTokenGenerator
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): ResetToken{
        return new ResetToken(
            (new TokenGenerator())->generate(),
            (new \DateTimeImmutable())->add($this->interval)
        );
    }
}