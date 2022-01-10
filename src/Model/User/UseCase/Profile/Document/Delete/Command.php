<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Document\Delete;


class Command
{

    public $fileId;

    public function __construct(string $fileId) {
        $this->fileId = $fileId;
    }

}