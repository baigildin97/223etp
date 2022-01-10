<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Buy;


class Command
{

    public $tariffId;

    public $userId;

    public function __construct(string $tariffId, string $userId) {
        $this->tariffId = $tariffId;
        $this->userId = $userId;
    }

}