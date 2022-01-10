<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Edit;

use App\ReadModel\Procedure\DetailView;
use Symfony\Component\Validator\Constraints as Assert;

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
    public $procedureType;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerFullName;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email()
     */
    public $organizerEmail;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerPhone;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $pricePresentationForm;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoPointEntry;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoTradingVenue;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoBiddingProcess;

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
     * @Assert\NotBlank()
     */
    public $startDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
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
    public $userId;

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
    public $depositPolicy;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $tenderingProcess;

    /**
     * Command constructor.
     * @param string $procedureId
     * @param string $userId
     * @param string $clientIp
     */
    public function __construct(string $procedureId, string $userId, string $clientIp)
    {
        $this->procedureId = $procedureId;
        $this->userId = $userId;
        $this->clientIp = $clientIp;
        $this->offerAuctionTime = '5';
    }


    public static function edit(string $procedureId, \App\ReadModel\Procedure\Lot\DetailView $detailView, string $userId, string $clientIp): self{
        $me = new self($procedureId, $userId, $clientIp);
        $me->procedureType = $detailView->type;
      //  $me->pricePresentationForm = $detailView->pricePresentationForm;

        $me->procedureName = $detailView->title;
        $me->arrestedPropertyType = $detailView->arrested_property_type;
        $me->pledgeer = $detailView->pledgeer;
        $me->region = $detailView->region;
        $me->location_object = $detailView->location_object;
        $me->additional_object_characteristics = $detailView->additional_object_characteristics;
        $me->starting_price = self::formatterMoney($detailView->starting_price);
        $me->nds = $detailView->nds;
        $me->reloadLot = $detailView->reload_lot;
        $me->startDateOfApplications = (new \DateTime($detailView->start_date_of_applications))->format("d.m.Y H:i");
        $me->closingDateOfApplications = (new \DateTime($detailView->closing_date_of_applications))->format("d.m.Y H:i");
        $me->summingUpApplications = (new \DateTime($detailView->summing_up_applications))->format("d.m.Y H:i");
        $me->startTradingDate = (new \DateTime($detailView->start_trading_date))->format("d.m.Y H:i");
        $me->auctionStep = self::formatterMoney($detailView->auction_step);
        $me->tenderBasic = $detailView->tender_basic;

        $me->bailiffsName = $detailView->bailiffs_name;
        $me->bailiffsNameDativeCase = $detailView->bailiffs_name_dative_case;
        $me->executiveProductionNumber = $detailView->executive_production_number;
        $me->dateEnforcementProceedings = $detailView->date_enforcement_proceedings;

        $me->organizerFullName = $detailView->organizer_full_name;
        $me->organizerEmail = $detailView->organizer_email;
        $me->organizerPhone = $detailView->organizer_phone;

        $me->deposit_amount = self::formatterMoney($detailView->deposit_amount);
        $me->advancePaymentTime = (new \DateTime($detailView->advance_payment_time))->format("d.m.Y H:i");
        $me->requisite = $detailView->requisite;

        $me->debtorFullName = $detailView->debtor_full_name;
        $me->debtorFullNameDateCase = $detailView->debtor_full_name_date_case;

        $me->infoPointEntry = $detailView->info_point_entry;
        $me->infoTradingVenue = $detailView->info_trading_venue;
        $me->infoBiddingProcess = $detailView->info_bidding_process;
        $me->depositPolicy = $detailView->deposit_policy;
        $me->tenderingProcess = $detailView->tendering_process;
        return $me;
    }

    private static function formatterMoney($money) {
        list($currency, $amount) = explode(' ', $money, 2);
        return $amount;
    }

}