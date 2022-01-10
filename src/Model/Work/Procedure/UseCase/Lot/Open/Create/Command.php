<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Create;

use Symfony\Component\Validator\Constraints as Assert;


class Command{

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureId;

    /**
     * @var int
     * @Assert\NotBlank
     */
    public $idNumber;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $arrestedPropertyType;

    /**
     * @var string
     */
    public $pledgeer;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $reloadLot;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $tenderBasic;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $bailiffsName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bailiffsNameDativeCase;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $executiveProductionNumber;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $offerAuctionTime;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $nds;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $dateEnforcementProceedings;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $auctionStep;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\LessThan(
     *     propertyPath="closingDateOfApplications",
     *     message="Дата началы подачи заявок должно быть меньше, чем даты окончание подачи заявок")
     */
    public $startDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\LessThan(
     *     propertyPath="summingUpApplications",
     *     message="Дата окончание подачи заявок должно быть меньше, чем даты подведения итогов заявок")
     */
    public $closingDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $summingUpApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $startTradingDate;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $debtorFullName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $debtorFullNameDateCase;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisite;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lotName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $region;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $location_object;

    /**
     * @var string
     */
    public $additional_object_characteristics;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $starting_price;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $advancePaymentTime;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $deposit_amount;

    /**
     * Command constructor.
     * @param string $procedureId
     * @param string $clientIp
     */
    public function __construct(string $procedureId, string $clientIp){
        $this->procedureId = $procedureId;
        $this->clientIp = $clientIp;
    }

    public function getValue($name){
        return $this->$name->getValue();
    }

}