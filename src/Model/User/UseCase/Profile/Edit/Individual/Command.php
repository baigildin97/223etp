<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Edit\Individual;

use App\Model\Profile\Entity\Profile\Files\CopyFileInn;
use App\Model\Profile\Entity\Profile\Files\CopyFilePassport;
use App\Model\Profile\Entity\Profile\Files\CopyFileSnils;
use App\Model\Profile\Entity\Profile\Files\FileQuestion;
use App\ReadModel\Profile\DetailView;
use Cassandra\Date;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

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
     */
    public $inn;

    /**
     * @var string
     */
    public $snils;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $factCountry;

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
     * @Assert\Length(min="4", max="4")
     */
    public $passportSeries;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="6")
     */
    public $passportNumber;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $passportIssuer;

    /**
     * @var DateTimeImmutable
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
    public $passportUnitCode;

    /**
     * @var DateTimeImmutable
     * @Assert\NotBlank()
     */
    public $birthDay;

    /**
     * @var string
    /**
     * @Assert\Url(
     *    protocols = {"http", "https"}
     * )
     */
    public $webSite;

    /**
     * @var string
     */
    public $clientIp;

    public function __construct(string $userId, string $clientIp) {
        $this->clientIp = $clientIp;
        $this->userId = $userId;
    }

    public static function edit(DetailView $detailView, string $clientIp): self{

        $me = new self($detailView->user_id, $clientIp);
        $me->birthDay = (new \DateTime($detailView->passport_birth_day))->format("d.m.Y");
        $me->citizenship = $detailView->passport_citizenship;
        $me->firstName = $detailView->repr_passport_first_name;
        $me->lastName = $detailView->repr_passport_last_name;
        $me->patronymic = $detailView->repr_passport_middle_name;
        $me->passportSeries = $detailView->passport_series;
        $me->passportNumber = $detailView->passport_number;
        $me->passportIssuer = $detailView->passport_issuer;
        $me->passportUnitCode = $detailView->passport_unit_code;
        $me->passportIssuanceDate =  (new \DateTime($detailView->passport_issuance_date))->format("d.m.Y");
        $me->phone = $detailView->phone;
        $me->webSite = $detailView->web_site;
        $me->inn = $detailView->passport_inn;
        $me->snils = $detailView->passport_snils;
        $me->legalIndex = $detailView->passport_legal_index;
        $me->legalCountry = $detailView->passport_legal_country;
        $me->legalRegion = $detailView->passport_legal_region;
        $me->legalCity = $detailView->passport_legal_city;
        $me->legalStreet = $detailView->passport_legal_street;
        $me->legalHouse = $detailView->passport_legal_house;
        $me->factIndex = $detailView->passport_fact_index;
        $me->factCountry = $detailView->passport_fact_country;
        $me->factRegion = $detailView->passport_fact_region;
        $me->factCity = $detailView->passport_fact_city;
        $me->factStreet = $detailView->passport_fact_street;
        $me->factHouse = $detailView->passport_fact_house;

        return $me;
    }
}
