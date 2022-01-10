<?php


namespace App\Tests\Unit\Model\Profile\Organization;


use App\Model\User\Entity\Profile\Organization\Organization;
use App\Tests\Builder\Certificate\CertificateBuilder;
use App\Tests\Builder\Profile\ProfileBuilder;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateIndividualEntrepreneur extends TestCase
{
    public function testSuccess(): void {
        $organization = Organization::createIndividualEntrepreneur(
            $profile = (new ProfileBuilder(
                $user = (new UserBuilder())->viaEmail()->build(),
                (new CertificateBuilder($user))->active()->build()
            ))->participant()->active()->build(),
            $inn = '123123123123', $legal = 'Ufa', $fact = 'Ufa',
            $org = 'MyOrg', $ogrn = '202020', $email = 'baigildin97@gmail.com', $time = new DateTimeImmutable());

        self::assertEquals($profile, $organization->getProfile());
        self::assertEquals($inn, $organization->getInn());
        self::assertEquals($legal, $organization->getLegalAddress());
        self::assertEquals($fact, $organization->getFactAddress());
        self::assertEquals($org, $organization->getFullTitleOrganization());
        self::assertEquals($ogrn, $organization->getOgrn());
        self::assertEquals($email, $organization->getEmail());
        self::assertEquals($time, $organization->getChangedAt());
    }
}