<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class BidRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Bid::class);
    }

    /**
     * @param Bid $bid
     */
    public function add( Bid $bid): void {
        $this->entityManager->persist($bid);
    }

    /**
     * @param Id $id
     * @return Bid|null|object
     */
    public function get(Id $id): ? Bid {
        if (!$lot = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Bid is not found.');
        }
        return $lot;
    }
}