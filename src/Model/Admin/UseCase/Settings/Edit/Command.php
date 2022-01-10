<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Settings\Edit;


class Command
{
    public $settingsId;

    public $value;

    public function __construct(string $settingsId) {
        $this->settingsId = $settingsId;
    }

    public static function edit(string $settingsId, string $value): self {
        $cm = new self($settingsId);
        $cm->value = $value;
        return $cm;
    }

}