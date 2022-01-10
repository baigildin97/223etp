<?php
declare(strict_types=1);
namespace App\Tests\Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\ResetToken;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\TokenGenerator;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Тестирование восстановления пароля.
 * Class ResetTest
 * @package App\Tests\Unit\Model\User\Entity\User\Reset
 */
class ResetTest extends TestCase
{
    /**
     * Успешный вариант. Сценарий: Сбрасываем пароль, сверяем ресет токен, меняем пароль сверяем хеш,
     */
    public function testSuccess(): void {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $now = new \DateTimeImmutable();
        $resetToken = new ResetToken(
            (new TokenGenerator())->generate(),
            $now->modify("+1 day")
        );
        $user->requestPasswordReset($resetToken, $now);
        self::assertNotNull($user->getResetToken());
        $user->passwordReset($now, $passwordHash = 'passwordHash');
        self::assertNull($user->getResetToken());
        self::assertEquals($passwordHash, $user->getPasswordHash());

    }

    /**
     * @throws \Exception
     * Вариант когда токен истек.
     * Сценарий создаем токен с текущим времением,
     * Отправляем токен на почту, сверяем токены, меняем пароль с текущем временем +1.
     */
    public function testExpiredToken(): void {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $now = new \DateTimeImmutable();
        $resetToken = new ResetToken(
            (new TokenGenerator())->generate(),
            $now
        );
        $user->requestPasswordReset($resetToken, $now);


        self::assertNotNull($user->getResetToken());
        self::assertEquals($resetToken, $user->getResetToken());
        $this->expectExceptionMessage("Reset token is expired.");
        $user->passwordReset($now->modify("+1 day"), $passwordHash = 'passwordHash');

    }

}