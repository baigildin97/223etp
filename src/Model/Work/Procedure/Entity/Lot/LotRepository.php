<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class LotRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Lot::class);
    }

    /**
     * @param Lot $lot
     */
    public function add(Lot $lot): void {
        $this->entityManager->persist($lot);
    }

    /**
     * @param Id $id
     * @return Lot|null|object
     */
    public function get(Id $id): ? Lot {
        if (!$lot = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Lot is not found.');
        }
        return $lot;
    }

    /**
     * @param \App\Model\Work\Procedure\Entity\Id $id
     * @return Lot|null|object
     */
    public function getByProcedureId(\App\Model\Work\Procedure\Entity\Id $id): ? Lot {
        if (!$lot = $this->repository->findOneBy(['procedure' => $id->getValue()])){
            throw new EntityNotFoundException('Lot is not found.');
        }
        return $lot;
    }
}