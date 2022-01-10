<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Payment\Requisite\Edit;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bankName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bankBik;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $paymentAccount;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $correspondentAccount;

    /**
     * @var string
     * @Assert\NotBlank()
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
     * @Assert\NotBlank()
     */
    public $requisiteId;

    /**
     * Command constructor.
     * @param string $paymentId
     * @param string $requisiteId
     */
    public function __construct(string $paymentId, string $requisiteId)
    {
        $this->paymentId = $paymentId;
        $this->requisiteId = $requisiteId;
    }

    /**
     * @param string $paymentId
     * @param string $requisiteId
     * @param string $bankName
     * @param string $bankBik
     * @param string $paymentAccount
     * @param string|null $bankAddress
     * @param string|null $personalAccount
     * @param string $correspondentAccount
     * @return static
     */
    public static function me(
        string $paymentId,
        string $requisiteId,
        string $bankName,
        string $bankBik,
        string $paymentAccount,
        ?string $bankAddress,
        ?string $personalAccount,
        string $correspondentAccount
    ): self
    {
        $me = new self($paymentId, $requisiteId);
        $me->bankBik = $bankBik;
        $me->bankName = $bankName;
        $me->paymentAccount = $paymentAccount;
        $me->correspondentAccount = $correspondentAccount;
        $me->bankAddress = $bankAddress;
        $me->personalAccount = $personalAccount;
        return $me;
    }
}