<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordHashGenerator;
use App\Model\User\Service\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Model\User\Entity\User\User;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $userRepository;

    private $hashGenerator;

    private $tokenGenerator;

    private $flusher;

    public static function getGroups(): array
    {
        return ['users'];
    }

    public function __construct(UserRepository $userRepository,
                                PasswordHashGenerator $hashGenerator,
                                TokenGenerator $tokenGenerator,
                                Flusher $flusher
    )
    {
        $this->userRepository = $userRepository;
        $this->hashGenerator = $hashGenerator;
        $this->tokenGenerator = $tokenGenerator;
        $this->flusher = $flusher;
    }

    public function load(ObjectManager $manager)
    {
        $user = User::signUpByEmail(
            $user_id = Id::next(),
            new \DateTimeImmutable(),
            new Email("admin@yandex.ru"),
            $this->hashGenerator->hash("admin123"),
            $token = $this->tokenGenerator->generate(),
            '127.0.0.1'
        );
        $this->userRepository->add($user);

        $user = User::signUpByEmail(
            $user_id_2 = Id::next(),
            new \DateTimeImmutable(),
            new Email("moderator@yandex.ru"),
            $this->hashGenerator->hash("moderator"),
            $token = $this->tokenGenerator->generate(),
            '127.0.0.1'
        );
        $this->userRepository->add($user);

        $this->flusher->flush();

        $user1 = $this->userRepository->get($user_id);
        $user1->confirmSignUp();
        $user1->changeRole(Role::admin());

        $user2 = $this->userRepository->get($user_id_2);
        $user2->confirmSignUp();
        $user2->changeRole(Role::moderator());

        $this->flusher->flush();


    }

}