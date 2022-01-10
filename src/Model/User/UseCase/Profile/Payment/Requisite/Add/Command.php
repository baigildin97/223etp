<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Add;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @var string
     * @Assert\Length(min="20", max="20")
     */
    public $paymentAccount;

    /**
     * @var string
     */
    public $bankName;

    /**
     * @var string
     * @Assert\Length(min="9", max="9")
     */
    public $bankBik;

    /**
     * @var string
     * @Assert\Length(min="20", max="20")
     */
    public $correspondentAccount;

    /**
     * @var string
     */
    public $paymentId;

    /**
     * @var string
     */
    public $bankAddress;

    /**
     * @var string
     */
    public $personalAccount;

    /**
     * @var string
     */
    public $status;

    public function __construct(string $paymentId) {
        $this->paymentId = $paymentId;
    }

}