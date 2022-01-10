<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Files\Sign;

class Command
{
    public $id;
    public $sign;
    public $clientIp;

    public function __construct(string $id, string $sign, string $clientIp)
    {
        $this->id = $id;
        $this->sign = $sign;
        $this->clientIp = $clientIp;
    }
}