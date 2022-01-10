<?php


namespace App\Tests\Unit\Model\Certificate;


use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Certificate\Id;
use App\Model\User\Entity\Certificate\IssuerName;
use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\Certificate\SubjectName;
use App\Model\User\Entity\User\Email;
use App\Model\User\Service\Certificate\IssuerConverter\IssuerConverter;
use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverter;
use App\Tests\Builder\User\UserBuilder;

class AddCertificateTest extends \PHPUnit\Framework\TestCase
{

    public function testSuccess(): void
    {
        $subjectName = new SubjectName(
            new SubjectConverter(
                'ОГРН=08397114189, СНИЛС=08397114189,
                         ИНН=540136524508, 
                         E=evdokim.vit@mail.ru, 
                         CN=Евдокимов Виталий Александрович, 
                         SN=Евдокимов, 
                         G=Виталий Александрович,
                         C=RU,
                         L=Санкт-Петербург, 
                         S=78 г. Санкт-Петербург'
            )
        );
        $issuerName = new IssuerName(
            new IssuerConverter(
                'CN="ООО ""КОМПАНИЯ ""ТЕНЗОР""",
                        O="ООО ""КОМПАНИЯ ""ТЕНЗОР""",
                        OU=Удостоверяющий центр, 
                        STREET="Московский проспект, д. 12", 
                        L=г. Ярославль, 
                        S=76 Ярославская область, 
                        C=RU, 
                        ИНН=007605016030, 
                        ОГРН=1027600787994, 
                        E=ca_tensor@tensor.ru'
            )
        );

        $userBuilder = new UserBuilder();

        $certificate = new Certificate($id = Id::next(),
            $user = $userBuilder->viaEmail(new Email('baigildin97@gmail.com'),
                'hash', 'token')->build(),
            $thumbprint = '123456789',
            $subjectName, $issuerName,
            $from = new \DateTimeImmutable(),
            $to = new \DateTimeImmutable(),
            $createdAt = new \DateTimeImmutable(),
            $status = Status::moderate());

        self::assertEquals($id, $certificate->getId());
        self::assertEquals($user, $certificate->getUser());
        self::assertEquals($thumbprint, $certificate->getThumbprint());
        self::assertEquals($subjectName, $certificate->getSubjectName());
        self::assertEquals($issuerName, $certificate->getIssuerName());
        self::assertEquals($from, $certificate->getValidFrom());
        self::assertEquals($to, $certificate->getValidTo());
        self::assertEquals($createdAt, $certificate->getCreatedAt());
        self::assertEquals($status, $certificate->getStatus());
    }

}