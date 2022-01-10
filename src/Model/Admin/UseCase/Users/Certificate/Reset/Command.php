<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Certificate\Reset;


class Command
{

    public $profileId;

    public $comment;

    public $moderatorId;

    public function __construct(string $profileId, string $moderatorId)
    {
        $this->profileId = $profileId;
        $this->moderatorId = $moderatorId;
    }

}