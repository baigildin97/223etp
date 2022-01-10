<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\EditBank;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     */
    public $user_id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bankName;

    /**
     * @var string
     * @Assert\Length(min=20, max=20)
     */
    public $correspondentAccount;

    /**
     * @var string
     * @Assert\Length(min=20, max=20)
     */
    public $paymentAccount;

    /**
     * @var string
     * @Assert\Length(min=9, max=9)
     */
    public $bankBik;

    public $requisite_id;

    public function __construct(string $user_id)
    {
        $this->user_id = $user_id;
    }

    public static function toEditBankInfo(string $user_id, string $bankName, string $bankBik,
                                          string $paymentAccount, string $correspondentAccount): self
    {
        $me = new self($user_id);

        $me->correspondentAccount = $correspondentAccount;
        $me->paymentAccount = $paymentAccount;
        $me->bankName = $bankName;
        $me->bankBik = $bankBik;

        return $me;
    }
}