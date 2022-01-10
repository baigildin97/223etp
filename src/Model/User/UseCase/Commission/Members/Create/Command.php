<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Create;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\Profile\UseCase\Commission\Members\Create
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $commissionId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $middleName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $position;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $role;

    /**
     * @var string
     */
    public $status;

    /**
     * Command constructor.
     * @param string $commissionId
     */
    public function __construct(string $commissionId) {
        $this->commissionId = $commissionId;
    }

}