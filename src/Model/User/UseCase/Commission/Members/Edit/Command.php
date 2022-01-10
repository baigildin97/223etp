<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Edit;


use App\Model\User\Entity\Commission\Members\Member;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $memberId;

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
     * @param string $memberId
     */
    private function __construct(string $memberId) {
        $this->memberId = $memberId;
    }

    public static function form(Member $member): self {
        $command = new self($member->getId()->getValue());
        $command->lastName = $member->getLastName();
        $command->firstName = $member->getFirstName();
        $command->middleName = $member->getMiddleName();
        $command->position = $member->getPositions();
        $command->role = $member->getRole();
        $command->status = $member->getStatus()->getValue();
        return $command;
    }
}