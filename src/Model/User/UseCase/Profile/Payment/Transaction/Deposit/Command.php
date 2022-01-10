<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Deposit;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $paymentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisiteId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $money;

    public function __construct(string $paymentId) {
        $this->paymentId = $paymentId;
    }
}