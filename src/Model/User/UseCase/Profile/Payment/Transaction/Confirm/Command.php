<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Confirm;


class Command
{
    public $transactionId;

    public $paymentId;

    public function __construct(string $transactionId, string $paymentId) {
        $this->transactionId = $transactionId;
        $this->paymentId = $paymentId;
    }
}