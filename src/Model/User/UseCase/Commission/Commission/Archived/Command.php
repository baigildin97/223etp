<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Archived;


class Command
{
    public $commissionId;

    public function __construct(string $commissionId) {
        $this->commissionId = $commissionId;
    }

}