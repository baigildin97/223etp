<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Tariff;


use App\Model\User\Entity\Profile\Tariff\Status;

class DetailView
{
    public $id;

    public $title;

    public $cost;

    public $period;

    public $status;

    public $created_at;

    public $archived_at;

    public $default_percent;

    public function isArchived(): bool {
        return $this->archived_at === Status::archived()->getName();
    }

}