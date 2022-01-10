<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Archive;

class Command
{
    public $profileId;

    public function __construct(string $profileId) {
        $this->profileId = $profileId;
    }
}