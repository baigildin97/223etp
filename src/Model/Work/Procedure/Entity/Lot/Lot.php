<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Entity\Lot;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Auction;
use App\Model\Work\Procedure\Entity\Lot\Bid;
use App\Model\Work\Procedure\Entity\Procedure;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * Class Procedure
 * @package App\Model\Work\Procedure\Entity\Lot
 * @ORM\Entity()
 * @ORM\Table(name="lots")
 */
class Lot
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="lot_id")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $idNumber;

    /**
     * @var string
     * @ORM\Column(type="string", name="arrested_property_type")
     */
    private $arrestedPropertyType;

    /**
     * @var Reload
     * @ORM\Column(type="reload_lot_type", name="reload_lot")
     */
    private $reloadLot;

    /**
     * @var Status
     * @ORM\Column(type="lot_status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="text", name="tender_basic")
     */
    private $tenderBasic;

    /**
     * @var string
     * @ORM\Column(type="string", name="date_enforcement_proceedings")
     */
    private $dateEnforcementProceedings;

    /**
     * @var Nds
     * @ORM\Column(type="nds_type")
     */
    private $nds;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="start_date_of_applications")
     */
    private $startDateOfApplications;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="closing_date_of_applications")
     */
    private $closingDateOfApplications;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="summing_up_applications")
     */
    private $summingUpApplications;

    /**
     * @var string
     * @ORM\Column(type="string", name="debtor_full_name")
     */
    private $debtorFullName;

    /**
     * @var string
     * @ORM\Column(type="string", name="debtor_full_name_date_case")
     */
    private $debtorFullNameDateCase;

    /**
     * @var string
     * @ORM\Column(type="text", name="deposit_policy")
     */
    private $depositPolicy;

    /**
     * @var string
     * @ORM\Column(type="text", name="lot_name")
     */
    private $lotName;

    /**
     * @var string
     * @ORM\Column(type="string", name="region")
     */
    private $region;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $pledgeer;

    /**
     * @var string
     * @ORM\Column(type="string", name="location_object")
     */
    private $location_object;

    /**
     * @var string
     * @ORM\Column(type="text", name="additional_object_characteristics", nullable=true)
     */
    private $additional_object_characteristics;

    /**
     * @var Money
     * @ORM\Column(type="money", name="starting_price")
     */
    private $starting_price;

    /**
     * @var string
     * @ORM\Column(type="text", name="bailiffs_name")
     */
    private $bailiffsName;

    /**
     * @var string
     * @ORM\Column(type="text", name="bailiffs_name_dative_case")
     */
    private $bailiffsNameDativeCase;

    /**
     * @var string
     * @ORM\Column(type="text", name="executive_production_number")
     */
    private $executiveProductionNumber;

    /**
     * @var Procedure
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Procedure", cascade={"persist"})
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id")
     */
    private $procedure;

    /**
     * @var Auction\Auction
     * @ORM\OneToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Auction\Auction", cascade={"persist"})
     * @ORM\JoinColumn(name="auction_id", referencedColumnName="id", nullable=false)
     */
    private $auction;

    /**
     * @var Bid\Bid|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", mappedBy="lot", orphanRemoval=true, cascade={"all"})
     */
    private $bids;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    private $clientIp;

    /**
     * @var Document\Document|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Document\Document", mappedBy="lot", cascade={"all"})
     */
    private $documents;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

//    /**
//     * @var Requisite
//     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Requisite\Requisite", inversedBy="bids")
//     * @ORM\JoinColumn(name="requisite_id", referencedColumnName="id", nullable=true)
//     */
//    private $requisite;
    /**
     * @var string
     * @ORM\Column(type="text", name="requisite")
     */
    private $requisite;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="advance_payment_time")
     */
    private $advancePaymentTime;

    /**
     * @var Money
     * @ORM\Column(type="money", name="deposit_amount")
     */
    private $deposit_amount;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $payment_winner_confirm;

    public function __construct(
        Id $id,
        int $idNumber,
        string $arrestedPropertyType,
        Reload $reloadLot,
        string $tenderBasic,
        Nds $nds,
        string $dateEnforcementProceedings,
        Status $status,
        \DateTimeImmutable $startDateOfApplications,
        \DateTimeImmutable $closingDateOfApplications,
        \DateTimeImmutable $summingDateUpApplications,
        string $debtorFullName,
        string $debtorFullNameDateCase,
        \DateTimeImmutable $advancePaymentTime,
        string $requisite,
        string $lotName,
        string $region,
        string $location_object,
        ?string $additional_object_characteristics,
        Money $starting_price,
        Money $deposit_amount,
        string $depositPolicy,
        string $bailiffsName,
        string $bailiffsNameDativeCase,
        ?string $pledgeer,
        string $executiveProductionNumber,
        Procedure $procedure,
        string $clientIp,
        \DateTimeImmutable $createdAt
    )
    {
        $this->id = $id;
        $this->idNumber = $idNumber;
        $this->arrestedPropertyType = $arrestedPropertyType;
        $this->reloadLot = $reloadLot;
        $this->tenderBasic = $tenderBasic;
        $this->nds = $nds;
        $this->dateEnforcementProceedings = $dateEnforcementProceedings;
        $this->status = $status;
        $this->startDateOfApplications = $startDateOfApplications;
        $this->closingDateOfApplications = $closingDateOfApplications;
        $this->summingUpApplications = $summingDateUpApplications;
        $this->debtorFullName = $debtorFullName;
        $this->debtorFullNameDateCase = $debtorFullNameDateCase;
        $this->advancePaymentTime = $advancePaymentTime;
        $this->requisite = $requisite;
        $this->lotName = $lotName;
        $this->region = $region;
        $this->location_object = $location_object;
        $this->additional_object_characteristics = $additional_object_characteristics;
        $this->starting_price = $starting_price;
        $this->deposit_amount = $deposit_amount;
        $this->depositPolicy = $depositPolicy;
        $this->bailiffsName = $bailiffsName;
        $this->bailiffsNameDativeCase = $bailiffsNameDativeCase;
        $this->pledgeer = $pledgeer;
        $this->executiveProductionNumber = $executiveProductionNumber;
        $this->procedure = $procedure;
        $this->bids = new ArrayCollection();
        $this->clientIp = $clientIp;
        $this->createdAt = $createdAt;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getAdditionalObjectCharacteristics(): ?string
    {
        return $this->additional_object_characteristics;
    }


    /**
     * @return Bid\Bid|ArrayCollection
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @return Auction\Auction
     */
    public function getAuction(): Auction\Auction
    {
        return $this->auction;
    }

    /**
     * @return string
     */
    public function getArrestedPropertyType(): string
    {
        return $this->arrestedPropertyType;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getClosingDateOfApplications(): \DateTimeImmutable
    {
        return $this->closingDateOfApplications;
    }

    /**
     * @return string
     */
    public function getDebtorFullName(): string
    {
        return $this->debtorFullName;
    }

    /**
     * @return string
     */
    public function getDebtorFullNameDateCase(): string
    {
        return $this->debtorFullNameDateCase;
    }

    /**
     * @return int
     */
    public function getIdNumber(): int
    {
        return $this->idNumber;
    }

    /**
     * @return string
     */
    public function getLocationObject(): string
    {
        return $this->location_object;
    }

    /**
     * @return string
     */
    public function getLotName(): string
    {
        return $this->lotName;
    }

    /**
     * @return Nds
     */
    public function getNds(): Nds
    {
        return $this->nds;
    }

    /**
     * @return Procedure
     */
    public function getProcedure(): Procedure
    {
        return $this->procedure;
    }

    /**
     * @return string
     */
    public function getBailiffsName(): string
    {
        return $this->bailiffsName;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Document\Document|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return string
     */
    public function getExecutiveProductionNumber(): string
    {
        return $this->executiveProductionNumber;
    }

    /**
     * @return null|string
     */
    public function getPledgeer(): ?string
    {
        return $this->pledgeer;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return Reload
     */
    public function getReloadLot(): Reload
    {
        return $this->reloadLot;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartDateOfApplications(): \DateTimeImmutable
    {
        return $this->startDateOfApplications;
    }

    /**
     * @return Money
     */
    public function getStartingPrice(): Money
    {
        return $this->starting_price;
    }

    /**
     * @return Money
     */
    public function getDepositAmount(): Money
    {
        return $this->deposit_amount;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSummingUpApplications(): \DateTimeImmutable
    {
        return $this->summingUpApplications;
    }

    /**
     * @return string
     */
    public function getTenderBasic(): string
    {
        return $this->tenderBasic;
    }

    /**
     * @return string
     */
    public function getFullNumber(): string
    {
        return (string)$this->procedure->getIdNumber();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAdvancePaymentTime(): \DateTimeImmutable
    {
        return $this->advancePaymentTime;
    }

    /**
     * @return string
     */
    public function getDateEnforcementProceedings(): string
    {
        return $this->dateEnforcementProceedings;
    }

    /**
     * @return string
     */
    public function getRequisite(): string
    {
        return $this->requisite;
    }

    /**
     * @return string
     */
    public function getBailiffsNameDativeCase(): string
    {
        return $this->bailiffsNameDativeCase;
    }

    /**
     * @return ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getApprovedBids()
    {
        $criteriaWhere = new Criteria();
        $expr = new Comparison('status', '=', Bid\Status::approved()->getName());
        $criteriaWhere->where($expr);
        return $this->getBids()->matching($criteriaWhere);
    }


    /**
     * @param Auction\Id $id
     * @param string $offerAuctionTime
     * @param Money $auctionStep
     * @param \DateTimeImmutable $startTradingDate
     */
    public function addAuction(Auction\Id $id,
                               string $offerAuctionTime,
                               Money $auctionStep,
                               \DateTimeImmutable $startTradingDate): void
    {
        $this->auction = new Auction\Auction(
            $id,
            Auction\Status::wait(),
            $this->starting_price,
            $this,
            new \DateTimeImmutable(),
            $offerAuctionTime,
            $auctionStep,
            $startTradingDate,
            $this->clientIp
        );
    }

    /**
     * @param Bid\Id $id
     * @param Bid\Status $status
     * @param Profile $participant
     * @param string $clientIp
     * @param \DateTimeImmutable $createdAt
     * @param Requisite $requisite
     * @param SubscribeTariff $subscribeTariff
     */
    public function addBid(
        Bid\Id $id,
        int $number,
        Bid\Status $status,
        Profile $participant,
        string $clientIp,
        \DateTimeImmutable $createdAt,
        Requisite $requisite,
        SubscribeTariff $subscribeTariff
    ): void
    {

        if (!$this->status->isAcceptingApplications()) {
            throw new \DomainException('Не возможно добавить заявок');
        }

        $this->bids->add(new Bid\Bid(
            $id,
            $number,
            $status,
            $participant,
            $this,
            $clientIp,
            $createdAt,
            $requisite,
            $subscribeTariff
        ));
    }

    public function addDocument(
        Document\Id $id,
        Document\Status $status,
        Document\File $file,
        \DateTimeImmutable $createdAt,
        string $clientIp,
        string $documentName
    ): void
    {
        if (!$this->status->isNew()) {
            throw new \DomainException('Не удалось загрузить файл.');
        }
        $this->documents->add(
            new Document\Document(
                $id,
                $status,
                $file,
                $createdAt,
                $this,
                $clientIp,
                $documentName
            )
        );
    }

    /**
     * Одобрение лота/для модератора
     */
    public function moderated(): void
    {
        $this->status = Status::moderated();
    }

    public function activate(): void
    {
        if (!$this->status->isModerated()) {
            throw new \DomainException('Лот не промодерирован.');
        }
        $this->status = Status::active();
    }

    /**
     * На модерации
     */
    public function moderate(): void
    {
        foreach ($this->getDocuments() as $document) {
            if (!$document->getStatus()->isSigned()) {
                throw new \DomainException('Подписаны не все документы лота.');
            }
        }
        $this->status = Status::moderate();
    }

    /**
     * отклонения лота/для модератора
     */
    public function reject(): void
    {
        $this->status = Status::rejected();
    }

    /**
     * Отзыв из модерации
     */
    public function recall(): void
    {
        if (!$this->status->isModerate()) {
            throw new \DomainException('Запрос на отзыв из модерации отклонен.');
        }

        $this->status = Status::new();
    }

    /**
     * Отмена публикации
     */
    public function cancelPublished(): void
    {
        $this->status = Status::new();
    }

    /**
     * Несостоялась
     */
    public function failed(): void
    {
        $this->status = Status::failed();
    }

    /**
     * Прием заявок статус
     */
    public function acceptingApplications(): void
    {
        $this->status = Status::acceptingApplications();
    }

    /**
     * Окончен прием заявок/статус
     */
    public function applicationsReceived(): void
    {
        $this->status = Status::applicationsReceived();
    }

    /**
     * Подписание победителем протокола о результатов торгов
     */
    public function statusSignedProtocolResult(): void
    {
        $this->status = Status::statusSignedProtocolResult();
    }

    /**
     * Подведение итогов приема заявок/статус
     */
    public function statusSummingUpApplications(): void
    {
        $this->status = Status::statusSummingUpApplications();
        $this->procedure->statusSummingUpApplications();
    }

    /**
     * Начало торгов/статус
     */
    public function startOfTrading(): void
    {
        $this->status = Status::statusBiddingProcess();
        $this->procedure->startOfTrading();
    }

    /**
     * Ожидание начала торгов
     */
    public function tradingWait(): void
    {
        $this->status = Status::statusStartOfTrading();
    }

    /**
     * Остановка торгов/статус
     */
    public function stopOfTrading(): void
    {
        $this->status = Status::statusBiddingCompleted();
        $this->procedure->stopOfTrading();
    }

    /**
     * Анулирование результатов торгов
     */
    public function cancellationProtocolResult()
    {
        $this->status = Status::cancellationProtocolResult();
    }

    /**
     * @param Profile $profile
     * @return Bid\Bid
     */
    public function findBidByProfile(Profile $profile): Bid\Bid
    {
        $criteriaWhere = new Criteria();
        $expr = new Comparison('participant', '=', $profile);
        $criteriaWhere->where($expr);
        $bid = $this->getBids()->matching($criteriaWhere);
        return $bid->first();
    }


    public function edit(
        string $procedureName,
        string $infoPointEntry,
        string $infoTradingVenue,
        string $infoBiddingProcess,
        string $arrestedPropertyType,
        Reload $reloadLot,
        string $tenderBasic,
        Nds $nds,
        ?string $pledgeer,
        string $bailiffsName,
		string $bailiffsNameDativeCase,
        string $dateEnforcementProceedings,
        string $executiveProductionNumber,
        Money $auctionStep,
        \DateTimeImmutable $startDateOfApplications,
        \DateTimeImmutable $closingDateOfApplications,
        \DateTimeImmutable $summingUpApplications,
        \DateTimeImmutable $startTradingDate,
        string $debtorFullName,
        string $debtorFullNameDateCase,
        \DateTimeImmutable $advancePaymentTime,
        string $requisite,
        string $lotName,
        string $region,
        string $location_object,
        ?string $additional_object_characteristics,
        Money $startingPrice,
        Money $depositAmount,
        string $depositPolicy,
        string $offerAuctionTime,
        string $tenderingProcess,
        string $clientIp,
        string $organizerFullName,
        string $organizerEmail,
        string $organizerPhone
    ): void {
//        if (!$this->status->isNew() and !$this->status->isRejected()) {
//            throw new \DomainException('Запрос на редактирование отклонен.');
//        }

        $this->status = Status::new();


        $this->procedure->edit(
            $procedureName,
            $infoPointEntry,
            $infoTradingVenue,
            $infoBiddingProcess,
            $tenderingProcess,
            $organizerFullName,
            $organizerEmail,
            $organizerPhone
        );

        $this->arrestedPropertyType = $arrestedPropertyType;
        $this->reloadLot = $reloadLot;
        $this->tenderBasic = $tenderBasic;
        $this->nds = $nds;
        $this->pledgeer = $pledgeer;
        $this->bailiffsName = $bailiffsName;
		$this->bailiffsNameDativeCase = $bailiffsNameDativeCase;
        $this->executiveProductionNumber = $executiveProductionNumber;
        $this->auction->edit($offerAuctionTime, $auctionStep, $startTradingDate);

        $this->startDateOfApplications = $startDateOfApplications;
        $this->closingDateOfApplications = $closingDateOfApplications;
        $this->summingUpApplications = $summingUpApplications;

        $this->dateEnforcementProceedings = $dateEnforcementProceedings;
        $this->advancePaymentTime = $advancePaymentTime;
        $this->debtorFullName = $debtorFullName;
        $this->debtorFullNameDateCase = $debtorFullNameDateCase;
        $this->requisite = $requisite;
        $this->lotName = $lotName;
        $this->region = $region;
        $this->location_object = $location_object;
        $this->additional_object_characteristics = $additional_object_characteristics;
        $this->starting_price = $startingPrice;
        $this->depositPolicy = $depositPolicy;
        $this->deposit_amount = $depositAmount;
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function getDepositPolicy(): string
    {
        return $this->depositPolicy;
    }

    public function cancel(): void
    {
        $this->status = Status::cancel();
    }

    public function pause(): void
    {
        $this->status = Status::pause();
    }

    public function resume(): void
    {
        $this->status = Status::resume();
    }

    public function paymentWinnerConfirm(): void
    {
        $this->payment_winner_confirm = true;
        $this->status = Status::statusBiddingCompleted();
    }

    public function paymentWinnerAnnulled(): void
    {
        $this->payment_winner_confirm = false;
    }
}