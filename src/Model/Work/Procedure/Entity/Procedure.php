<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Entity;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Organization\Organization;
use App\Model\User\Entity\Profile\Representative\Representative;
use App\Model\Work\Procedure\Entity\Document\ProcedureDocument;
use App\Model\Work\Procedure\Entity\Lot\Auction\Id as AuctionId;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\Protocol\Protocol;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\XmlDocument as ProtocolXmlDocument;
use App\Model\Work\Procedure\Entity\XmlDocument\History\Action;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;
use App\Model\Work\Procedure\Entity\Document\Id as FileId;
use App\Model\Work\Procedure\Entity\Document\File;
use App\Model\Work\Procedure\Entity\Document\FileType;


/**
 * Class Procedure
 * @package App\Model\Work\Procedure\Entity
 * @ORM\Entity()
 * @ORM\Table(name="procedures")
 */
class Procedure
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="procedure_id")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="id_number", type="integer")
     */
    private $idNumber;

    /**
     * @var PriceForm
     * @ORM\Column(type="procedure_price_form", name="price_presentation_form")
     */
    private $pricePresentationForm;

    /**
     * @var ConductingType
     * @ORM\Column(type="conducting_type", name="conducting_type")
     */
    private $conductingType;

    /**
     * @var string
     * @ORM\Column(type="text", name="info_point_entry")
     */
    private $infoPointEntry;

    /**
     * @var string
     * @ORM\Column(type="text", name="info_trading_venue")
     */
    private $infoTradingVenue;

    /**
     * @var string
     * @ORM\Column(type="text", name="info_bidding_process")
     */
    private $infoBiddingProcess;

    /**
     * @var string
     * @ORM\Column(type="text", name="tendering_process")
     */
    private $tenderingProcess;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Contract
     * @ORM\Column(type="procedure_contract_type")
     */
    private $contractType;

    /**
     * @var Status
     * @ORM\Column(type="procedure_status")
     */
    private $status;

    /**
     * @var Type
     * @ORM\Column(type="procedure_type")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $organizerFullName;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $organizerEmail;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $organizerPhone;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $organizer;

    /**
     * @var Organization
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Organization\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $organization;

    /**
     * @var Representative
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Representative\Representative", cascade={"persist"})
     * @ORM\JoinColumn(name="representative_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $representative;

    /**
     * @var Lot | ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Lot", mappedBy="procedure", cascade={"persist"})
     */
    private $lots;

    /**
     * @var ArrayCollection|ProcedureDocument[]
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Document\ProcedureDocument", mappedBy="procedure", orphanRemoval=true, cascade={"persist"})
     */
    private $documents;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip")
     */
    private $clientIp;

    /**
     * @var XmlDocument\XmlDocument|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\XmlDocument\XmlDocument", mappedBy="bid", cascade={"all"})
     */
    private $xmlDocuments;

    /**
     * @var Protocol|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Protocol\Protocol", mappedBy="procedure", cascade={"persist"})
     */
    private $protocols;


    /**
     * Procedure constructor.
     * @param Id $id
     * @param int $idNumber
     * @param string $title
     * @param \DateTimeImmutable $createdAt
     * @param Contract $contractType
     * @param Type $type
     * @param string $organizerFullName
     * @param string $organizerEmail
     * @param string $organizerPhone
     * @param Status $status
     * @param Profile $organizer
     * @param Organization $organization
     * @param Representative $representative
     * @param PriceForm $pricePresentationForm
     * @param string $infoPointEntry
     * @param string $infoTradingVenue
     * @param string $infoBiddingProcess
     * @param string $tenderingProcess
     * @param string $clientIp
     * @param ConductingType $conductingType
     * @param int|null $reloadIdNumber
     */
    public function __construct(
        Id $id,
        int $idNumber,
        string $title,
        \DateTimeImmutable $createdAt,
        Contract $contractType,
        Type $type,
        string $organizerFullName,
        string $organizerEmail,
        string $organizerPhone,
        Status $status,
        Profile $organizer,
        Organization $organization,
        Representative $representative,
        PriceForm $pricePresentationForm,
        string $infoPointEntry,
        string $infoTradingVenue,
        string $infoBiddingProcess,
        string $tenderingProcess,
        string $clientIp,
        ConductingType $conductingType
    )
    {
        $this->id = $id;
        $this->idNumber = $idNumber;
        $this->organizer = $organizer;
        $this->organization = $organization;
        $this->representative = $representative;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->contractType = $contractType;
        $this->type = $type;
        $this->organizerFullName = $organizerFullName;
        $this->organizerEmail = $organizerEmail;
        $this->organizerPhone = $organizerPhone;
        $this->status = $status;
        $this->pricePresentationForm = $pricePresentationForm;
        $this->infoPointEntry = $infoPointEntry;
        $this->infoTradingVenue = $infoTradingVenue;
        $this->infoBiddingProcess = $infoBiddingProcess;
        $this->tenderingProcess = $tenderingProcess;
        $this->lots = new ArrayCollection();
        $this->protocols = new ArrayCollection();
        $this->clientIp = $clientIp;
        $this->documents = new ArrayCollection();
        $this->conductingType = $conductingType;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
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
    public function getOrganizerPhone(): string
    {
        return $this->organizerPhone;
    }

    /**
     * @return string
     */
    public function getOrganizerFullName(): string
    {
        return $this->organizerFullName;
    }

    /**
     * @return string
     */
    public function getOrganizerEmail(): string
    {
        return $this->organizerEmail;
    }

    public function getPricePresentationForm(): PriceForm{
        return $this->pricePresentationForm;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function get(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return ProcedureDocument[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @return Protocol|ArrayCollection
     */
    public function getProtocols()
    {
        return $this->protocols;
    }

    /**
     * @return XmlDocument\XmlDocument|ArrayCollection
     */
    public function getXmlDocuments()
    {
        return $this->xmlDocuments;
    }

    /**
     * @param Id $id
     */
    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @return Contract
     */
    public function getContractType(): Contract
    {
        return $this->contractType;
    }

    /**
     * @return int
     */
    public function getIdNumber(): int
    {
        return $this->idNumber;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Contract $contractType
     */
    public function setContractType(Contract $contractType): void
    {
        $this->contractType = $contractType;
    }

    /**
     * @param IdNumber $idNumber
     */
    public function setIdNumber(IdNumber $idNumber): void
    {
        $this->idNumber = $idNumber;
    }

    /**
     * @param ProcedureDocument $procedureDocument
     */
    public function addFile(ProcedureDocument $procedureDocument): void
    {
        if (!$this->status->isNew() and !$this->status->isRejected()) {
            throw new \DomainException('Не удалось загрузить документ.');
        }
        //dd($procedureDocument);
        $this->documents->add($procedureDocument);
    }

    public function dublicateFile(
        string $path,
        string $documentName,
        int $size,
        string $name,
        ?string $hash,
        string $fileType): void{

        $newDocument = new ProcedureDocument(
            FileId::next(),
            $this,
            new File(
                $path,
                $documentName,
                $size,
                $name,
                $hash
            ),
            new \DateTimeImmutable(),
            \App\Model\Work\Procedure\Entity\Document\Status::new(),
            new FileType($fileType)
        );

        $this->documents->add(
            $newDocument
        );
    }


    /**
     * @return string
     */
    public function getInfoBiddingProcess(): string
    {
        return $this->infoBiddingProcess;
    }

    /**
     * @return string
     */
    public function getInfoPointEntry(): string
    {
        return $this->infoPointEntry;
    }

    /**
     * @return string
     */
    public function getTenderingProcess(): string{
        return $this->tenderingProcess;
    }

    /**
     * @return string
     */
    public function getInfoTradingVenue(): string
    {
        return $this->infoTradingVenue;
    }

    /**
     * @return Lot|ArrayCollection
     */
    public function getLots()
    {
        return $this->lots;
    }

    /**
     * @return Organization
     */
    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @return Profile
     */
    public function getOrganizer(): Profile
    {
        return $this->organizer;
    }


    /**
     * @return Representative
     */
    public function getRepresentative(): Representative
    {
        return $this->representative;
    }

    /**
     * Добавление лота к процедуре и создание к нему аукциона
     * @param int $idNumber
     * @param string $arrestedPropertyType
     * @param Reload $reloadLot
     * @param string $tenderBasic
     * @param Nds $nds
     * @param string $dateEnforcementProceedings
     * @param \DateTimeImmutable $startDateOfApplications
     * @param \DateTimeImmutable $closingDateOfApplications
     * @param \DateTimeImmutable $summingUpApplications
     * @param string $debtorFullName
     * @param string $debtorFullNameDateCase
     * @param \DateTimeImmutable $advancePaymentTime
     * @param string $requisite
     * @param string $lotName
     * @param string $region
     * @param string $location_object
     * @param string|null $additional_object_characteristics
     * @param Money $starting_price
     * @param Money $deposit_amount
     * @param string $depositPolicy
     * @param string $bailiffsName
     * @param string $bailiffsNameDativeCase
     * @param string|null $pledgeer
     * @param string $executiveProductionNumber
     * @param string $clientIp
     * @param string $offerAuctionTime
     * @param Money $auctionStep
     * @param \DateTimeImmutable $startTradingDate
     */
    public function addLot(
        int $idNumber,
        string $arrestedPropertyType,
        Reload $reloadLot,
        string $tenderBasic,
        Nds $nds,
        string $dateEnforcementProceedings,
        \DateTimeImmutable $startDateOfApplications,
        \DateTimeImmutable $closingDateOfApplications,
        \DateTimeImmutable $summingUpApplications,
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
        string $clientIp,
        string $offerAuctionTime,
        Money $auctionStep,
        \DateTimeImmutable $startTradingDate
    ){

        $this->lots->add(
            $lot = new Lot(
                \App\Model\Work\Procedure\Entity\Lot\Id::next(),
                $idNumber,
                $arrestedPropertyType,
                $reloadLot,
                $tenderBasic,
                $nds,
                $dateEnforcementProceedings,
                \App\Model\Work\Procedure\Entity\Lot\Status::new(),
                $startDateOfApplications,
                $closingDateOfApplications,
                $summingUpApplications,
                $debtorFullName,
                $debtorFullNameDateCase,
                $advancePaymentTime,
                $requisite,
                $lotName,
                $region,
                $location_object,
                $additional_object_characteristics,
                $starting_price,
                $deposit_amount,
                $depositPolicy,
                $bailiffsName,
                $bailiffsNameDativeCase,
                $pledgeer,
                $executiveProductionNumber,
                $this,
                $clientIp,
                new \DateTimeImmutable())
        );

        $lot->addAuction(
            AuctionId::next(),
            $offerAuctionTime,
            $auctionStep,
            $startTradingDate
        );
    }


    public function createNotice(
        XmlDocument\Id $id,
        int $number,
        XmlDocument\Status $status,
        XmlDocument\Type $type,
        string $xml,
        string $hash,
        \DateTimeImmutable $signedAt,
        XmlDocument\StatusTactic $statusTactic,
        string $clientIp
    ): void
    {

        if (!$this->status->isNew() && !$this->status->isRejected()) {
            throw new \DomainException('Не удалось отправить запрос на модерацию.');
        }

        if ($this->documents->count() === 0) {
            throw new \DomainException('Не все документы процедуры загружены.');
        }

        foreach ($this->documents as $document) {
            if ($document->getStatus()->isNew()) {
                throw new \DomainException('Подписаны не все документы процедуры');
            }
        }

        $xmlDocument = new XmlDocument\XmlDocument(
            $id,
            $number,
            $status,
            $type,
            $xml,
            $hash,
            $this,
            $signedAt,
            $statusTactic
        );

        $xmlDocument->addHistory(
            \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
            $this->getOrganizer()->getUser(),
            Action::send(),
            new \DateTimeImmutable(),
            $clientIp
        );

        $this->xmlDocuments->add($xmlDocument);

        $this->status = Status::moderate();

        foreach ($this->getLots() as $lot) {
            $lot->moderate();
        }
    }

    /**
     * Публикация процедуры
     */
    public function activate(): void
    {
        if (!$this->status->isModerated()) {
            throw new \DomainException('Процедура не промодерирована.');
        }
        $this->status = Status::active();
        foreach ($this->getLots() as $lot) {
            $lot->activate();
        }
    }


    public function recall(): void
    {
        if (!$this->status->isModerate()) {
            throw new \DomainException('Извещение не находиться на модерации.');
        }

        $this->status = Status::new();
        foreach ($this->getLots() as $lot) {
            $lot->recall();
        }
    }


    /**
     * Отмена публикации
     */
    public function cancelPublished(): void{
        $this->status = Status::new();
        foreach ($this->getLots() as $lot) {
            $lot->cancelPublished();
        }
    }

    /**
     * Одобрение процедуры/для модератора
     */
    public function moderated(): void
    {
        $this->status = Status::moderated();
        foreach ($this->lots as $lot) {
            $lot->moderated();
        }
    }

    /**
     * отклонения процедуры/для модератора
     */
    public function reject(): void
    {
        $this->status = Status::rejected();
        foreach ($this->lots as $lot) {
            $lot->reject();
        }
    }

    /**
     * Прием заявок/статус
     */
    public function acceptingApplications(): void
    {
        $this->status = Status::acceptingApplications();
        foreach ($this->lots as $lot) {
            $lot->acceptingApplications();
        }
    }

    /**
     * Окончен прием заявок/статус
     */
    public function applicationsReceived(): void
    {
        $this->status = Status::applicationsReceived();
        foreach ($this->lots as $lot) {
            $lot->applicationsReceived();
        }
    }

    /**
     * Подведение итогов приема заявок/статус
     */
    public function statusSummingUpApplications(): void
    {
        $this->status = Status::statusSummingUpApplications();
//        foreach ($this->lots as $lot){
//            $lot->statusSummingUpApplications();
//        }
    }

    /**
     * Начало торгов/статус
     */
    public function startOfTrading(): void
    {
        $this->status = Status::statusBiddingProcess();
//        foreach ($this->lots as $lot){
//            $lot->startOfTrading();
//        }
    }

    /**
     * Несостоялась
     */
    public function failed(): void
    {
        $this->status = Status::failed();
        foreach ($this->lots as $lot) {
            $lot->failed();
        }
    }

    /**
     * Ожидание начала торгов
     */
    public function tradingWait(): void
    {
        $this->status = Status::statusStartOfTrading();
        foreach ($this->lots as $lot) {
            $lot->tradingWait();
        }
    }

    /**
     * Остановка торгов/статус
     */
    public function stopOfTrading(): void
    {
        $this->status = Status::statusBiddingCompleted();
        /*    foreach ($this->lots as $lot) {
                $lot->stopOfTrading();
            }*/
    }

    /**
     * Анулирование результатов торгов
     */
    public function cancellationProtocolResult()
    {
        $this->status = Status::cancellationProtocolResult();
        foreach ($this->lots as $lot) {
            $lot->cancellationProtocolResult();
        }
    }

    /**
     * Подписание победителем протокола о результатов торгов
     */
    public function signedWinner(): void
    {
        $this->status = Status::statusSignedProtocolResult();
        foreach ($this->lots as $lot) {
            $lot->statusSignedProtocolResult();
        }

        //   $this->organizer->getUser()
    }

    public function addProtocol(
        \App\Model\Work\Procedure\Entity\Protocol\Id $id,
        \App\Model\Work\Procedure\Entity\Protocol\IdNumber $idNumber,
        \App\Model\Work\Procedure\Entity\Protocol\Type $type,
        \App\Model\Work\Procedure\Entity\Protocol\Status $status,
        ProtocolXmlDocument $xmlDocument,
        \DateTimeImmutable $createdAt,
        Reason $reason,
        string $organizerComment
    ): void
    {
        $newProtocol = new Protocol(
            $id,
            $idNumber,
            $type,
            $this,
            $xmlDocument,
            $status,
            $createdAt,
            $reason,
            $organizerComment
        );

        foreach ($this->protocols as $protocol){
            if ($protocol->getType()->isEqual($newProtocol->getType())){
                throw new \DomainException('Данный протокол уже сформирован');

            }
        }
        $this->protocols->add($newProtocol);
    }


    public function edit(string $title,
                         string $infoPointEntry,
                         string $infoTradingVenue,
                         string $infoBiddingProcess,
                         string $tenderingProcess,
                         string $organizerFullName,
                         string $organizerEmail,
                         string $organizerPhone
    ): void
    {
//        if (!$this->status->isNew() and !$this->status->isRejected()) {
//            throw new \DomainException('Запрос на редактирование отклонен.');
//        }

        $this->status = Status::new();

        $this->infoPointEntry = $infoPointEntry;
        $this->infoTradingVenue = $infoTradingVenue;
        $this->infoBiddingProcess = $infoBiddingProcess;
        $this->organizerFullName = $organizerFullName;
        $this->organizerEmail = $organizerEmail;
        $this->organizerPhone = $organizerPhone;
        $this->title = $title;
        $this->tenderingProcess = $tenderingProcess;
    }

    public function cancel(): void
    {
        $this->status = Status::cancel();
        foreach ($this->lots as $lot) {
            $lot->cancel();
        }
    }

    public function pause(): void
    {
        $this->status = Status::pause();
        foreach ($this->lots as $lot) {
            $lot->pause();
        }
    }

    public function resume(): void
    {
        $this->status = Status::resume();
        foreach ($this->lots as $lot) {
            $lot->resume();
        }
    }
}