<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Participant\Individual;


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
     * @var \DateTime
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
     * @var \DateTime
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
        $this->clientIp = $clientIp;
        $this->userId = $userId;
    }
}