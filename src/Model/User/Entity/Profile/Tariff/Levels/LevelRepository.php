<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff\Levels;


use Doctrine\ORM\EntityManagerInterface;
use App\Model\EntityNotFoundException;

class LevelRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Level::class);
    }

    /**
     * @param Level $rate
     */
    public function add(Level $rate): void {
        $this->entityManager->persist($rate);
    }

    /**
     * @param Id $id
     * @return Level|null|object
     */
    public function get(Id $id): ? Level {
        if (!$profile = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Level is not found.');
        }
        return $profile;
    }
}