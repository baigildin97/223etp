<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Role\Edit;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $id;
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $roleConstant;

    /**
     * @var string[]
     */
    public $permissions;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromRole(string $id, string $name, string $roleConstant, array $permissions): self
    {
        $command = new self($id);
        $command->name = $name;
        $command->roleConstant = $roleConstant;
        $command->permissions = $permissions;
        return $command;
    }
}