<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Create;


class Command
{

    public $title;

    public $status;

    public $profileId;

    public function __construct(string $profileId) {
        $this->profileId = $profileId;
    }

}