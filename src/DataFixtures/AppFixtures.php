<?php

namespace App\DataFixtures;

use App\Container\Model\SubscribeTariff\SubscribeTariffFactory;
use App\Model\Admin\Entity\Settings\Id;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Admin\Entity\Settings\Settings;
use App\Model\Admin\Entity\Settings\SettingsRepository;
use App\Model\User\Entity\Profile\Tariff\Levels\Level;
use App\Model\User\Entity\Profile\Tariff\Levels\LevelRepository;
use App\Model\User\Entity\Profile\Tariff\Status;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use App\Model\User\Entity\Profile\Tariff\TariffRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Money;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $settingsRepository;

    private $tariffRepository;

    private $levelRepository;

    private $subscribeTariffFactory;

    public static function getGroups(): array
    {
        return ['settings'];
    }

    public function __construct(SettingsRepository $settingsRepository,
                                TariffRepository $tariffRepository,
                                LevelRepository $levelRepository,
                                SubscribeTariffFactory $subscribeTariffFactory
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->tariffRepository = $tariffRepository;
        $this->levelRepository = $levelRepository;
        $this->subscribeTariffFactory = $subscribeTariffFactory;
    }

    /**
     * Основные настройки
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        foreach (Key::$defaultKeys as $key => $val) {
            if ($setting = $this->settingsRepository->findByKey(new Key($key))) {
                $setting->update(new Key($key), $val);
            } else {
                $settings = new Settings(
                    Id::next(),
                    new Key($key),
                    $val
                );
                $this->settingsRepository->add($settings);
            }
        }

        $tariff = new Tariff(
            new \App\Model\User\Entity\Profile\Tariff\Id($this->subscribeTariffFactory->create()),
            'Базовый',
            Money::RUB(0),
            12,
            Status::active(),
            new \DateTimeImmutable(),
            1
        );
        $this->tariffRepository->add($tariff);
        $manager->flush();

        $level = new Level(
            \App\Model\User\Entity\Profile\Tariff\Levels\Id::next(),
            1,
            $this->tariffRepository->get(new \App\Model\User\Entity\Profile\Tariff\Id($this->subscribeTariffFactory->create())),
            Money::RUB(2000000000),
            4,
            new \DateTimeImmutable(),
        );
        $this->levelRepository->add($level);

        $level = new Level(
            \App\Model\User\Entity\Profile\Tariff\Levels\Id::next(),
            2,
            $this->tariffRepository->get(new \App\Model\User\Entity\Profile\Tariff\Id($this->subscribeTariffFactory->create())),
            Money::RUB(1000000000),
            3,
            new \DateTimeImmutable(),
        );
        $this->levelRepository->add($level);

        $manager->flush();
    }
}