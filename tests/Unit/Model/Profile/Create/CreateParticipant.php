<?php


namespace App\Tests\Unit\Model\Profile\Create;


use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\Profile\Role;
use App\Model\User\Entity\User\Email;
use App\Tests\Builder\Certificate\CertificateBuilder;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use Zend\EventManager\Exception\DomainException;

class CreateParticipant extends TestCase
{
    public function testSuccess(): void {
        $profile = new Profile(Type::participant(), $time = new DateTimeImmutable(),
            $user = (new UserBuilder())->viaEmail()->build(),
            $certificate = (new CertificateBuilder($user))->active()->build());

        self::assertEquals($user, $profile->getUser());
        self::assertEquals($certificate, $profile->getCertificate());
        self::assertEquals($time, $profile->getCreatedAt());
        self::assertEquals(Type::participant(), $profile->getType());
        self::assertEquals(Status::wait(), $profile->getStatus());
    }

    public function testInconsistent(): void {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Сертификат и профиль принадлежат разным пользователям');

        $profile = new Profile(Type::participant(), $time = new DateTimeImmutable(),
            $user = (new UserBuilder())->viaEmail()->build(),
            $certificate = (new CertificateBuilder())->active()->build());
    }
}