<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Role\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Role\Id;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Role\RoleRepository;

class Handler
{
    private $roles;
    private $flusher;

    public function __construct(RoleRepository $roles, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if ($this->roles->hasByName($command->name, $command->roleConstant)) {
            throw new \DomainException('Role already exists.');
        }

        $role = new Role(
            Id::next(),
            $command->name,
            $command->roleConstant,
            $command->permissions
        );

        $this->roles->add($role);

        $this->flusher->flush();
    }
}