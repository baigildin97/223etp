<?php
declare(strict_types=1);
namespace App\Tests\Builder\Certificate;


use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Certificate\Id;
use App\Model\User\Entity\Certificate\IssuerName;
use App\Model\User\Entity\Certificate\Status;
use App\Model\User\Entity\Certificate\SubjectName;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\Certificate\IssuerConverter\IssuerConverter;
use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverter;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable as DTImmutable;

class CertificateBuilder
{
    private $id;
    private $createdAt;
    private $user;
    private $thumbprint;
    private $subjectName;
    private $issuerName;
    private $validFrom;
    private $validTo;
    private $status;

    public function __construct(User $user = null, string $thumbprint = null,
                                SubjectName $subjectName = null, IssuerName $issuerName = null,
                                DTImmutable $validFrom = null, DTImmutable $validTo = null) {

        $this->id = Id::next();
        $this->createdAt = new \DateTimeImmutable();

        $this->user = $user ?? (new UserBuilder())->viaEmail()->confirmed()->build();
        $this->thumbprint = $thumbprint ?? '123456';
        $this->subjectName = $subjectName ?? new SubjectName(
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
        $this->issuerName = $issuerName ?? new IssuerName(
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
        $this->validFrom = $validFrom ?? DTImmutable::createFromFormat(
                'd-m-Y/G:i:s', '01-01-2010/00:00:00');

        $this->validTo = $validTo ?? DTImmutable::createFromFormat(
                'd-m-Y/G:i:s', '01-01-2020/00:00:00');
    }

    public function archived(): self {
        $clone = clone $this;
        $clone->status = Status::archived();

        return $clone;
    }

    public function moderate(): self {
        $clone = clone $this;
        $clone->status = Status::moderate();

        return $clone;
    }

    public function rejected(): self {
        $clone = clone $this;
        $clone->status = Status::reject();

        return $clone;
    }

    public function active(): self {
        $clone = clone $this;
        $clone->status = Status::active();

        return $clone;
    }

    public function build(): Certificate {
        return new Certificate($this->id, $this->user, $this->thumbprint, $this->subjectName,
            $this->issuerName, $this->validFrom, $this->validTo, $this->createdAt, $this->status);
    }
}