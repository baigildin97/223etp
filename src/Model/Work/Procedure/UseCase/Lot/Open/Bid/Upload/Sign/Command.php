<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload\Sign;


class Command
{
    public $documentId;
    public $sign;
    public $clientIp;
    public $bidId;
    public $userId;

    public function __construct(string $documentId, string $bidId, string $sign, string $clientIp, string $userId)
    {
        $this->documentId = $documentId;
        $this->sign = $sign;
        $this->clientIp = $clientIp;
        $this->bidId = $bidId;
        $this->userId = $userId;
    }
}
