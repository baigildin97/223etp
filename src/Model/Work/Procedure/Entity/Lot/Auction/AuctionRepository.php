<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Auction;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class AuctionRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Auction::class);
    }

    /**
     * @param Auction $auction
     */
    public function add(Auction $auction): void
    {
        $this->entityManager->persist($auction);
    }

    /**
     * @param Id $id
     * @return Auction|null|object
     */
    public function get(Id $id): ?Auction
    {
        if (!$auction = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Auction is not found.');
        }
        return $auction;
    }


}