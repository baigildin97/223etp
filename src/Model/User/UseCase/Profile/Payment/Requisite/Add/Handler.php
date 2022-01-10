<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Add;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Id;
use App\Model\User\Entity\Profile\Payment\PaymentRepository;
use App\Model\User\Entity\Profile\Requisite\Id as RequisiteId;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(Flusher $flusher, PaymentRepository $paymentRepository) {
        $this->flusher = $flusher;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $payment = $this->paymentRepository->get(new Id($command->paymentId));

        $payment->addRequisite(
            RequisiteId::next(),
            $command->paymentAccount,
            $command->personalAccount,
            $command->bankName,
            $command->bankBik,
            $command->correspondentAccount,
            $command->bankAddress,
            new \DateTimeImmutable()
        );

        $this->flusher->flush();
    }

}