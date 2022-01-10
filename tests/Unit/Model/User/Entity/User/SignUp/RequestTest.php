<?php
declare(strict_types=1);
namespace App\Tests\Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;


class RequestTest extends TestCase
{

    public function testSuccess():void {
        $user = User::signUpByEmail(
            $id = Id::next(),
            $createdAt = new \DateTimeImmutable(),
            $email = new Email("test@mail.ru"),
            $name = new Name('Наиль', 'Исмагилов', 'Галиевич'),
            $password = "hash",
            $confirmToken = 'token'
        );

        self::assertTrue($user->getStatus()->isWait());
        self::assertFalse($user->getStatus()->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($createdAt, $user->getCreatedAt());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($name, $user->getName());
        self::assertEquals($password, $user->getPasswordHash());

        self::assertTrue($user->getRole()->isUser());
    }

}