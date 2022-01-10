<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Settings;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class SettingsRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Settings::class);
    }

    /**
     * @param Settings $settings
     */
    public function add(Settings $settings): void {
        $this->entityManager->persist($settings);
    }

    /**
     * @param Id $id
     * @return Settings|null|object
     */
    public function get(Id $id): ? Settings {
        if (!$settings = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Settings is not found.');
        }
        return $settings;
    }

    public function findByKey(Key $key): ? Settings{
        return $this->repository->findOneBy(['key' => $key]);
    }
}