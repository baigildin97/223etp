<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Edit;



class Command
{
    public $title;

    public $status;

    public $userId;

    public $id;

    public function __construct(string $userId) {
        $this->userId = $userId;
    }

    public static function edit(string $userId, array $commission): self {
        $editForm = new self($userId);
        $editForm->status = $commission['status'];
        $editForm->title = $commission['title'];
        $editForm->id = $commission['id'];
        return $editForm;
    }
}