<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Cancel;


class Command
{

    /**
     * @var string
     */
    public $transactionId;

    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string
     */
    public $paymentId;

    public function __construct(string $transactionId, string $paymentId, string $user_id) {
        $this->transactionId = $transactionId;
        $this->paymentId = $paymentId;
        $this->user_id = $user_id;
    }

}