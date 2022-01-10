<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Settings\Add;


use App\Model\Admin\Entity\Settings\Id;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Admin\Entity\Settings\Settings;
use App\Model\Admin\Entity\Settings\SettingsRepository;
use App\Model\Flusher;
use App\ReadModel\Admin\Settings\SettingsFetcher;

/**
 * Class Handler
 * @package App\Model\Admin\UseCase\Settings\Add
 */
class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param SettingsRepository $settingsRepository
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        Flusher $flusher,
        SettingsRepository $settingsRepository,
        SettingsFetcher $settingsFetcher
    ) {
        $this->flusher = $flusher;
        $this->settingsRepository = $settingsRepository;
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        if ($this->settingsFetcher->existsByKey($command->key)){
            throw new \DomainException('Parameter already exists.');
        }

        $settings = new Settings(Id::next(), new Key($command->key), $command->value);

        $this->settingsRepository->add($settings);

        $this->flusher->flush();
    }

}