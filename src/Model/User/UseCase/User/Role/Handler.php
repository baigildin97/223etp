<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Role;


use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepositoryInterface;

class Handler
{

    private $users;
    private $flusher;

    public function __construct(UserRepositoryInterface $userRepository, Flusher $flusher)
    {
        $this->users = $userRepository;
        $this->flusher = $flusher;
    }


    public function handle(Command $command): void {

        $user = $this->users->get(new Id($command->id));

        $user->changeRole(new Role($command->role));

        $this->flusher->flush();
    }

}