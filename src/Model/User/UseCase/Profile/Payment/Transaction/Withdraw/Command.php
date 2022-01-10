<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw
 */
class Command
{
    /**
     * @var string|int
     * @Assert\NotBlank()
     */
    public $money;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $paymentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisite;

    /**
     * Command constructor.
     * @param string $paymentId
     */
    public function __construct(string $paymentId){
        $this->paymentId = $paymentId;
    }

}