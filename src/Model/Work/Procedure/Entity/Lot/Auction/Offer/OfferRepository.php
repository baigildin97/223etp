<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Auction\Offer;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
class OfferRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Offer::class);
    }

    /**
     * @param Offer $offer
     */
    public function add(Offer $offer): void {
        $this->entityManager->persist($offer);
    }

    /**
     * @param Id $id
     * @return Offer|null|object
     */
    public function get(Id $id): ? Offer {
        if (!$lot = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Offer is not found.');
        }
        return $lot;
    }
}