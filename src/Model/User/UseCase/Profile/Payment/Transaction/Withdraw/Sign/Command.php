<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisiteId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $paymentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlDocument;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     */
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $money;

    /**
     * Command constructor.
     * @param string $xmlDocument
     * @param string $hash
     * @param string $requisiteId
     * @param string $paymentId
     * @param string $money
     */
    public function __construct(string $xmlDocument, string $hash, string $requisiteId, string $paymentId, string $money){
        $this->xmlDocument = $xmlDocument;
        $this->hash = $hash;
        $this->requisiteId = $requisiteId;
        $this->paymentId = $paymentId;
        $this->money = $money;
    }
}