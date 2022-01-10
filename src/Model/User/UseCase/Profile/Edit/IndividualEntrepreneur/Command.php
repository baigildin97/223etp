<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\IndividualEntrepreneur;

use App\ReadModel\Profile\DetailView;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="15", max="15")
     */
    public $ogrnip;

    /**
     * @var string
     */
    public $inn;

    /**
     * @var string
     */
    public $snils;

    /**
     * @var string
     */
    public $position;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $firstName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lastName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $patronymic;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factCountry;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factRegion;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factCity;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factIndex;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factStreet;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factHouse;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalCountry;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalRegion;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalCity;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalIndex;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalStreet;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $legalHouse;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $passportSeries;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $passportNumber;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $passportIssuer;

    /**
     * @var \DateTimeImmutable
     * @Assert\NotBlank()
     */
    public $passportIssuanceDate;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $citizenship;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $unitCode;

    /**
     * @var \DateTimeImmutable
     * @Assert\NotBlank()
     */
    public $birthDay;

    /**
     * @var string
     * /**
     * @Assert\Url(
     *    protocols = {"http", "https"}
     * )
     */
    public $webSite;

    /**
     * @var string
     */
    public $clientIp;

    public function __construct(string $userId, string $clientIp)
    {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }

    public static function edit(DetailView $detailView, string $clientIp): self
    {
        $me = new self($detailView->user_id, $clientIp);
        $me->position = $detailView->position;
        $me->firstName = $detailView->repr_passport_first_name;
        $me->lastName = $detailView->repr_passport_last_name;
        $me->patronymic = $detailView->repr_passport_middle_name;
        $me->birthDay = (new \DateTime($detailView->passport_birth_day))->format("d.m.Y");
        $me->citizenship = $detailView->passport_citizenship;
        $me->passportSeries = $detailView->passport_series;
        $me->passportNumber = $detailView->passport_number;
        $me->passportIssuer = $detailView->passport_issuer;
        $me->unitCode = $detailView->passport_unit_code;
        $me->passportIssuanceDate = (new \DateTime($detailView->passport_issuance_date))->format("d.m.Y");
        $me->phone = $detailView->phone;
        $me->inn = $detailView->inn;
        $me->snils = $detailView->passport_snils;
        $me->ogrnip = $detailView->ogrn;
        $me->webSite = $detailView->web_site;
        $me->legalIndex = $detailView->legal_index;
        $me->legalCountry = $detailView->legal_country;
        $me->legalRegion = $detailView->legal_region;
        $me->legalCity = $detailView->legal_city;
        $me->legalStreet = $detailView->legal_street;
        $me->legalHouse = $detailView->legal_house;
        $me->factIndex = $detailView->fact_index;
        $me->factCountry = $detailView->fact_country;
        $me->factRegion = $detailView->fact_region;
        $me->factCity = $detailView->fact_city;
        $me->factStreet = $detailView->fact_street;
        $me->factHouse = $detailView->fact_house;

        return $me;
    }
}