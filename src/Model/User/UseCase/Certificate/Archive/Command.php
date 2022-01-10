<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Certificate\Archive;


class Command
{
    public $certificateId;

    public function __construct(string $certificateId) {
        $this->certificateId = $certificateId;
    }
}