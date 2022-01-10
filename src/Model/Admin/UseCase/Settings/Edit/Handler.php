<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Settings\Edit;


use App\Model\Admin\Entity\Settings\Id;
use App\Model\Admin\Entity\Settings\SettingsRepository;
use App\Model\Flusher;

class Handler
{

    private $flusher;

    private $settingsRepository;

    public function __construct(Flusher $flusher, SettingsRepository $settingsRepository) {
        $this->flusher = $flusher;
        $this->settingsRepository = $settingsRepository;
    }

    public function handle(Command $command): void {
        $setting = $this->settingsRepository->get(new Id($command->settingsId));
        $setting->edit($command->value);
        $this->flusher->flush();
    }
}