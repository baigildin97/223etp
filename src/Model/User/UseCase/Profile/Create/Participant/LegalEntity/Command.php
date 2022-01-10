<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Participant\LegalEntity;


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
     * @Assert\Length(min="3", max="190")
     */
    public $position;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="190")
     */
    public $confirmingDocument;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $phone;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $shortTitleOrganization;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $fullTitleOrganization;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="9", max="9")
     */
    public $kpp;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="13", max="13")
     */
    public $ogrn;

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
     * @Assert\Length(min="20", max="20")
     */
    public $paymentAccount;

    /**
     * @var string
     * @Assert\Length(min="3", max="190")
     */
    public $bankName;

    /**
     * @var string
     * @Assert\Length(min="9", max="9")
     */
    public $bankBik;

    /**
     * @var string
     * @Assert\Length(min="20", max="20")
     */
    public $correspondentAccount;

    /**
     * @var string
     */
    public $fio;

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

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $representativeInn;

    public function __construct(string $userId, string $clientIp) {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }
}