<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\AddBank;


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

    /**
     * @var string
     */
    public $profileId;

    /**
     * Command constructor.
     * @param string $profileId
     */
    public function __construct(string $profileId) {
        $this->profileId = $profileId;
    }
}