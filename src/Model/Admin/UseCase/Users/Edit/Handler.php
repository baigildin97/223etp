<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Users\Edit;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private $flusher;
    private $userRepository;

    public function __construct(Flusher $flusher, UserRepository $userRepository){
        $this->flusher = $flusher;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command): void{
        $user = $this->userRepository->get(new Id($command->id_user));
        $user->changeRole(new Role($command->role));
        $this->flusher->flush();
    }

}