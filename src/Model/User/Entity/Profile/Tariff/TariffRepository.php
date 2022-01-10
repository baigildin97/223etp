<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class TariffRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Tariff::class);
    }

    /**
     * @param Tariff $rate
     */
    public function add(Tariff $rate): void {
        $this->entityManager->persist($rate);
    }

    /**
     * @param Id $id
     * @return Tariff|null|object
     */
    public function get(Id $id): ? Tariff {
        if (!$profile = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Tariff is not found.');
        }
        return $profile;
    }
}