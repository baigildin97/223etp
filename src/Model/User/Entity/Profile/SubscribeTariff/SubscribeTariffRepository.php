<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\SubscribeTariff;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class SubscribeTariffRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(SubscribeTariff::class);
    }

    /**
     * @param SubscribeTariff $SubscribeTariff
     */
    public function add(SubscribeTariff $SubscribeTariff): void {
        $this->entityManager->persist($SubscribeTariff);
    }

    /**
     * @param Id $id
     * @return SubscribeTariff|null|object
     */
    public function get(Id $id): ? SubscribeTariff {
        if (!$requisite = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('SubscribeTariff is not found.');
        }
        return $requisite;
    }

}