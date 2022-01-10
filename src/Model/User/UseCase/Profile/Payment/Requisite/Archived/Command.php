<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Archived;


class Command
{

    public $requisiteId;

    public function __construct(string $requisiteId)
    {
        $this->requisiteId = $requisiteId;
    }

}