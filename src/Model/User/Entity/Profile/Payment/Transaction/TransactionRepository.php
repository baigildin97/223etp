<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class TransactionRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Transaction::class);
    }

    /**
     * @param Transaction
     */
    public function add(Transaction $transaction): void {
        $this->entityManager->persist($transaction);
    }

    /**
     * @param Id $id
     * @return Transaction|null|object
     */
    public function get(Id $id): ? Transaction {
        if (!$profile = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Transaction is not found.');
        }
        return $profile;
    }

}