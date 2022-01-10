<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Participant\IndividualEntrepreneur;

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
     * @Assert\Length(min="3", max="190")
     */
    public $position;

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
     * @Assert\NotBlank()
     */
    public $certificate;

    /**
     * @var string
     */
    public $webSite;

    /**
     * @var string
     */
    public $clientIp;

    public function __construct(string $userId, string $clientIp) {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }
}