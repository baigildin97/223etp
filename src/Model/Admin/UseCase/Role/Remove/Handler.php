<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Role\Remove;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\Work\Entity\Role\Id;
use App\Model\Work\Entity\Role\RoleRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;

class Handler
{
    private $roles;
    private $profileRepository;
    private $flusher;

    public function __construct(RoleRepository $roles, ProfileRepository $profileRepository, Flusher $flusher)
    {
        $this->roles = $roles;
        $this->profileRepository = $profileRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $role = $this->roles->get(new Id($command->id));

        if ($this->profileRepository->hasWithRole($role->getId())) {
            throw new \DomainException('Role contains profiles.');
        }

        $this->roles->remove($role);

        $this->flusher->flush();
    }
}