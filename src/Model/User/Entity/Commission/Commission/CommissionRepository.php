<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Commission;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class CommissionRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Commission::class);
    }

    /**
     * @param Commission $commission
     */
    public function add(Commission $commission): void {
        $this->entityManager->persist($commission);
    }

    /**
     * @param Id $id
     * @return Commission|null|object
     */
    public function get(Id $id): ? Commission {
        if (!$commission = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Commission is not found.');
        }
        return $commission;
    }
}