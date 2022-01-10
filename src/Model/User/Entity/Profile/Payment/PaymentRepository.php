<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class PaymentRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Payment::class);
    }

    /**
     * @param Payment $payment
     */
    public function add(Payment $payment): void {
        $this->entityManager->persist($payment);
    }

    /**
     * @param Id $id
     * @return Payment|null|object
     */
    public function get(Id $id): ? Payment {
        if (!$profile = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Payment is not found.');
        }
        return $profile;
    }
}