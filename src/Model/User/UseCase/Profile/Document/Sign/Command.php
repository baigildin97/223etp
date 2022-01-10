<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Document\Sign;

class Command
{
    public $id;
    public $sign;
    public $clientIp;
    public $profileId;

    public function __construct(string $id, string $sign, string $clientIp, string $profileId)
    {
        $this->id = $id;
        $this->sign = $sign;
        $this->clientIp = $clientIp;
        $this->profileId = $profileId;
    }
}