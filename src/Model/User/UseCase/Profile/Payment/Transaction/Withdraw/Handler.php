<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Id;
use App\Model\User\Entity\Profile\Payment\PaymentRepository;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use Money\Currency;
use Money\Money;

class Handler
{

    private $flusher;

    private $paymentRepository;

    private $requisiteRepository;

    public function __construct(Flusher $flusher, PaymentRepository $paymentRepository, RequisiteRepository $requisiteRepository)
    {
        $this->flusher = $flusher;
        $this->paymentRepository = $paymentRepository;
        $this->requisiteRepository = $requisiteRepository;
    }

    public function handle(Command $command): void
    {
/*        $payment = $this->paymentRepository->get(new Id($command->paymentId));
        $requisite = $this->requisiteRepository->get(new \App\Model\User\Entity\Profile\Requisite\Id($command->requisiteId));


        $payment->withdraw(new Money($command->money, new Currency('RUB')), $requisite);

        $this->flusher->flush();*/
    }

}
