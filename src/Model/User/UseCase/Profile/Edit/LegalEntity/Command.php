<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Edit\LegalEntity;

use App\Helpers\Filter;
use App\ReadModel\Profile\DetailView;
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
    public $email;

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
     * @Assert\Length(min="3", max="190")
     */
    public $fullTitleOrganization;

    /**
     * @var string
     * @Assert\Length(min="3", max="190")
     */
    public $shortTitleOrganization;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="9", max="9")
     */
    public $kpp;

    /**
     * @var string
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
    /**
     * @Assert\Url(
     *    protocols = {"http", "https"}
     * )
     */
    public $webSite;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $representativeInn;

    /**
     * @var string
     */
    public $clientIp;

    public function __construct(string $userId, string $clientIp) {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }

    public static function edit(DetailView $detailView, string $clientIp): self{
        $me = new self($detailView->user_id, $clientIp);
        $me->position = $detailView->position;
        $me->confirmingDocument = $detailView->confirming_document;
        $me->phone = $detailView->phone;
        $me->email = $detailView->org_email;
        $me->firstName = $detailView->repr_passport_first_name;
        $me->lastName = $detailView->repr_passport_last_name;
        $me->patronymic = $detailView->repr_passport_middle_name;
        $me->webSite = $detailView->web_site;
        $me->inn = Filter::filterInnLegalEntity($detailView->inn);
        $me->shortTitleOrganization = $detailView->short_title_organization;
        $me->fullTitleOrganization = $detailView->full_title_organization;
        $me->ogrn = $detailView->ogrn;
        $me->kpp = $detailView->kpp;
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
        $me->representativeInn = $detailView->repr_passport_inn;

        return $me;
    }
}