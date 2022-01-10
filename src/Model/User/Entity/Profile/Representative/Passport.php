<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Representative;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Passport
 * @package App\Model\Profile\Entity\Profile
 * @ORM\Embeddable()
 */
class Passport
{
    /**
     * @var string
     * @ORM\Column(type="string", name="first_name")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", name="middle_name")
     */
    private $middleName;

    /**
     * @var string
     * @ORM\Column(type="string", name="last_name")
     */
    private $lastName;

    /**
     * Серия
     * @var string
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $series;

    /**
     * Номер
     * @var string
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $number;

    /**
     * Кем выдан
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $issuer;

    /**
     * Дата выдачи
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $issuanceDate;

    /**
     * Гражданство
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $citizenship;

    /**
     * Код подразделения
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $unitCode;

    /**
     * День рождения
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $birthDay;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factCountry;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factRegion;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factCity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factIndex;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factStreet;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $factHouse;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalCountry;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalRegion;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalCity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalIndex;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalStreet;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $legalHouse;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $inn;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $snils;

    /**
     * Passport constructor.
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     */
    private function __construct(
        string $firstName,
        string $middleName,
        string $lastName
    ) {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
    }

    /**
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $snils
     * @return static
     */
    public static function createLegalEntity(
        string $firstName,
        string $middleName,
        string $lastName,
        string $snils
    ): self {
        $me =  new self($firstName, $middleName, $lastName);
        $me->snils = $snils;
        return $me;
    }

    /**
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $series
     * @param string $number
     * @param string $issuer
     * @param \DateTime $issuanceDate
     * @param string $citizenship
     * @param string $unitCode
     * @param \DateTime $birthDay
     * @param string $factCountry
     * @param string $factRegion
     * @param string $factCity
     * @param string $factIndex
     * @param string $factStreet
     * @param string $factHouse
     * @param string $legalCountry
     * @param string $legalRegion
     * @param string $legalCity
     * @param string $legalIndex
     * @param string $legalStreet
     * @param string $legalHouse
     * @return static
     */
    public static function createIndividualEntrepreneur(
        string $firstName,
        string $middleName,
        string $lastName,
        string $series,
        string $number,
        string $issuer,
        \DateTimeImmutable $issuanceDate,
        string $citizenship,
        string $unitCode,
        \DateTimeImmutable $birthDay,
        string $factCountry,
        string $factRegion,
        string $factCity,
        string $factIndex,
        string $factStreet,
        string $factHouse,
        string $legalCountry,
        string $legalRegion,
        string $legalCity,
        string $legalIndex,
        string $legalStreet,
        string $legalHouse,
        string $snils
    ): self {
        $passport = new self($firstName, $middleName, $lastName);
        $passport->series = $series;
        $passport->number = $number;
        $passport->issuer = $issuer;
        $passport->issuanceDate = $issuanceDate;
        $passport->citizenship = $citizenship;
        $passport->unitCode = $unitCode;
        $passport->birthDay = $birthDay;
        $passport->factCountry = $factCountry;
        $passport->factRegion = $factRegion;
        $passport->factCity = $factCity;
        $passport->factIndex = $factIndex;
        $passport->factStreet = $factStreet;
        $passport->factHouse = $factHouse;
        $passport->legalCountry = $legalCountry;
        $passport->legalRegion = $legalRegion;
        $passport->legalCity = $legalCity;
        $passport->legalIndex = $legalIndex;
        $passport->legalStreet = $legalStreet;
        $passport->legalHouse = $legalHouse;
        $passport->snils = $snils;
        return $passport;
    }


    /**
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $series
     * @param string $number
     * @param string $issuer
     * @param \DateTime $issuanceDate
     * @param string $citizenship
     * @param string $unitCode
     * @param \DateTime $birthDay
     * @param string $factCountry
     * @param string $factRegion
     * @param string $factCity
     * @param string $factIndex
     * @param string $factStreet
     * @param string $factHouse
     * @param string $legalCountry
     * @param string $legalRegion
     * @param string $legalCity
     * @param string $legalIndex
     * @param string $legalStreet
     * @param string $legalHouse
     * @param string $inn
     * @param string $snils
     * @return static
     */
    public static function createIndividual(
        string $firstName,
        string $middleName,
        string $lastName,
        string $series,
        string $number,
        string $issuer,
        \DateTimeImmutable $issuanceDate,
        string $citizenship,
        string $unitCode,
        \DateTimeImmutable $birthDay,
        string $factCountry,
        string $factRegion,
        string $factCity,
        string $factIndex,
        string $factStreet,
        string $factHouse,
        string $legalCountry,
        string $legalRegion,
        string $legalCity,
        string $legalIndex,
        string $legalStreet,
        string $legalHouse,
        string $inn,
        string $snils
    ): self {
        $passport = new self($firstName, $middleName, $lastName);
        $passport->series = $series;
        $passport->number = $number;
        $passport->issuer = $issuer;
        $passport->issuanceDate = $issuanceDate;
        $passport->citizenship = $citizenship;
        $passport->unitCode = $unitCode;
        $passport->birthDay = $birthDay;
        $passport->factCountry = $factCountry;
        $passport->factRegion = $factRegion;
        $passport->factCity = $factCity;
        $passport->factIndex = $factIndex;
        $passport->factStreet = $factStreet;
        $passport->factHouse = $factHouse;
        $passport->legalCountry = $legalCountry;
        $passport->legalRegion = $legalRegion;
        $passport->legalCity = $legalCity;
        $passport->legalIndex = $legalIndex;
        $passport->legalStreet = $legalStreet;
        $passport->legalHouse = $legalHouse;
        $passport->inn = $inn;
        $passport->snils = $snils;
        return $passport;
    }


    /**
     * @return DateTimeImmutable
     */
    public function getBirthDay(): DateTimeImmutable
    {
        return $this->birthDay;
    }

    /**
     * @return string
     */
    public function getCitizenship(): string
    {
        return $this->citizenship;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPassportIssuanceDate(): DateTimeImmutable
    {
        return $this->issuanceDate;
    }

    public function setInn(string $inn): void
    {
        $this->inn = $inn;
    }

    /**
     * @return string
     */
    public function getPassportIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @return string
     */
    public function getPassportNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getPassportSeries(): string
    {
        return $this->series;
    }

    /**
     * @return string
     */
    public function getPassportUnitCode(): string
    {
        return $this->unitCode;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getIssuanceDate(): DateTimeImmutable
    {
        return $this->issuanceDate;
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @return string
     */
    public function getUnitCode(): string
    {
        return $this->unitCode;
    }

    /**
     * @return string
     */
    public function getFullName(): string {
        return $this->lastName." ".$this->firstName." ".$this->middleName;
    }


}