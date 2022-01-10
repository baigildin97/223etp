<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Reset\Reset;


use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepositoryInterface;
use App\Model\User\Service\PasswordHashGenerator;

class Handler
{
    private $users;
    private $flusher;
    private $passwordHashGenerator;

    public function __construct(UserRepositoryInterface $userRepository, PasswordHashGenerator $passwordHashGenerator, Flusher $flusher)
    {
        $this->users = $userRepository;
        $this->passwordHashGenerator = $passwordHashGenerator;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void {
        if (!$user = $this->users->findByResetToken($command->token)){
            throw new \DomainException("Incorrect or confirmed token.");
        }

        $user->passwordReset(
            new \DateTimeImmutable(),
            $this->passwordHashGenerator->hash($command->newPassword)
        );
        $this->flusher->flush();
    }

}