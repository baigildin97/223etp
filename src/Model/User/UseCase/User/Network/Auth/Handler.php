<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Network\Auth;



use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
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
        if ($this->users->hasByNetworkIdentity($command->network, $command->identity)){
            throw new \DomainException("User already exists.");
        }

        $user = User::signUpByNetwork(Id::next(), new \DateTimeImmutable(), $command->identity, $command->network);
        $this->users->add($user);
        $this->flusher->flush();
    }

}