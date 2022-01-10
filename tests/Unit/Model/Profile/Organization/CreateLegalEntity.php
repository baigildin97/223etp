<?php


namespace App\Tests\Unit\Model\Profile\Organization;


use App\Model\User\Entity\Profile\Organization\Organization;
use App\Tests\Builder\Certificate\CertificateBuilder;
use App\Tests\Builder\Profile\ProfileBuilder;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateLegalEntity extends TestCase
{
    public function testSuccess(): void {
        $organization = Organization::createLegalEntity($profile = (new ProfileBuilder(
            $user = (new UserBuilder())->viaEmail()->build(),
            (new CertificateBuilder($user))->active()->build()
        ))->participant()->active()->build(), $inn = '1234567890', $legal = 'Moskow', $fact = 'Moskow',
            $orgFull = 'MyOrgFull', $orgShort = 'MOF', $ogrn = '202020', $email = 'myorg@mail.ru',
            $kpp = '123321', $snils = '123454321', $time = new DateTimeImmutable());

        self::assertEquals($profile, $organization->getProfile());
        self::assertEquals($inn, $organization->getInn());
        self::assertEquals($legal, $organization->getLegalAddress());
        self::assertEquals($fact, $organization->getFactAddress());
        self::assertEquals($orgFull, $organization->getFullTitleOrganization());
        self::assertEquals($orgShort, $organization->getShortTitleOrganization());
        self::assertEquals($ogrn, $organization->getOgrn());
        self::assertEquals($email, $organization->getEmail());
        self::assertEquals($time, $organization->getChangedAt());
        self::assertEquals($kpp, $organization->getKpp());
        self::assertEquals($snils, $organization->getSnils());
    }
}