<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Edit;


use App\ReadModel\Procedure\Lot\DetailView;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureId;

    /**
     * @var string
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
     * @Assert\NotBlank()
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
     * @Assert\NotBlank
     */
    public $auctionStep;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $dateEnforcementProceedings;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\LessThan(
     *     propertyPath="closingDateOfApplications",
     *     message="Дата началы подачи заявок должно быть меньше, чем дата окончание подачи заявок")
     */
    public $startDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\LessThan(
     *     propertyPath="summingUpApplications",
     *     message="Дата окончание подачи заявок должно быть меньше, чем дата подведения итогов заявок")
     */
    public $closingDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     * @Assert\LessThan(
     *     propertyPath="startTradingDate",
     *     message="Дата подведения итогов приема заявок должно быть меньше, чем дата началы торгов")
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
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lotId;

    /**
     * @param string $userId
     * @param DetailView $detailView
     * @param string $client_ip
     * @return $this
     * @throws \Exception
     */
    public static function edit(string $userId, DetailView $detailView, string $client_ip): self{
        $me = new self();
        $me->userId = $userId;
        $me->lotId = $detailView->id;
        $me->procedureId = $detailView->procedure_id;
        $me->idNumber = $detailView->id_number;
        $me->arrestedPropertyType = $detailView->arrested_property_type;
        $me->pledgeer = $detailView->pledgeer;
        $me->reloadLot = $detailView->reload_lot;
        $me->tenderBasic = $detailView->tender_basic;
        $me->bailiffsName = $detailView->bailiffs_name;
        $me->bailiffsNameDativeCase = $detailView->bailiffs_name_dative_case;
        $me->executiveProductionNumber = $detailView->executive_production_number;
        $me->offerAuctionTime = $detailView->offer_auction_time;
        $me->nds = $detailView->nds;
        $me->startTradingDate = (new \DateTimeImmutable($detailView->start_trading_date))->format("d.m.Y H:i");
        $me->auctionStep = self::formatterMoney($detailView->auction_step);
        $me->dateEnforcementProceedings = $detailView->date_enforcement_proceedings;
        $me->startDateOfApplications = (new \DateTimeImmutable($detailView->start_date_of_applications))->format("d.m.Y H:i");
        $me->closingDateOfApplications = (new \DateTimeImmutable($detailView->closing_date_of_applications))->format("d.m.Y H:i");
        $me->summingUpApplications = (new \DateTimeImmutable($detailView->summing_up_applications))->format("d.m.Y H:i");
        $me->debtorFullName = $detailView->debtor_full_name;
        $me->debtorFullNameDateCase = $detailView->debtor_full_name_date_case;
        $me->lotName = $detailView->lot_name;
        $me->region = $detailView->region;
        $me->location_object = $detailView->location_object;
        $me->advancePaymentTime = $detailView->advance_payment_time;
        $me->deposit_amount = self::formatterMoney($detailView->deposit_amount);
        $me->requisite = $detailView->requisite;
        $me->additional_object_characteristics = $detailView->additional_object_characteristics;
        $me->starting_price = self::formatterMoney($detailView->starting_price);
        $me->clientIp = $client_ip;
        return $me;
    }


    private static function formatterMoney($money) {
        list($currency, $amount) = explode(' ', $money, 2);
        return $amount;
    }


}