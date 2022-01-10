<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure;

use App\Model\Front\Entity\OldProcedure\Document\Document;
use App\Model\Front\Entity\OldProcedure\Notice\Notice;
use App\Model\Front\Entity\OldProcedure\Protocols\Protocol;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Procedure
 * @package App\Model\Work\Procedure\Entity\OldProcedure
 * @ORM\Entity()
 * @ORM\Table(schema="old_records", name="procedures")
 */

class OldProcedure
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="old_procedure_id")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="id_number", type="integer")
     */
    private $idNumber;

    /**
     * @var string
     * @ORM\Column(type="text", name="tender_basic")
     */
    private $tenderBasic;

    /**
     * @var string
     * @ORM\Column(type="string", name="price_presentation_form")
     */
    private $pricePresentationForm;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer")
     */
    private $organizer;

    /**
     * @var string
     * @ORM\Column(type="string", name="start_date_of_applications")
     */
    private $startDateOfApplications;

    /**
     * @var string
     * @ORM\Column(type="string", name="closing_date_of_applications")
     */
    private $closingDateOfApplications;

    /**
     * @var string
     * @ORM\Column(type="string", name="start_trading_date")
     */
    private $startTradingDate;

    /**
     * @var string
     * @ORM\Column(type="string", name="status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", name="reload_lot")
     */
    private $reloadLot;

    /**
     * @var string
     * @ORM\Column(type="string", name="auction_step")
     */
    private $auctionStep;

    /**
     * @var string
     * @ORM\Column(type="string", name="start_cost")
     */
    private $start_cost;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $nds;

    /**
     * @var string
     * @ORM\Column(type="string", name="deposit_amount")
     */
    private $deposit_amount;

    /**
     * @var string
     * @ORM\Column(type="string", name="arrested_property_type")
     */
    private $arrestedPropertyType;

    /**
     * @var string
     * @ORM\Column(type="text", name="additional_object_characteristics", nullable=true)
     */
    private $additionalObjectCharacteristics;

    /**
     * @var string
     * @ORM\Column(type="string", name="region")
     */
    private $region;

    /**
     * @var string
     * @ORM\Column(type="string", name="location_object")
     */
    private $locationObject;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_short_name")
     */
    private $organizerShortName;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_full_name")
     */
    private $organizerFullName;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_contact_full_name")
     */
    private $organizerContactFullName;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_phone")
     */
    private $organizerPhone;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_email")
     */
    private $organizerEmail;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_legal_address")
     */
    private $organizerLegalAddress;

    /**
     * @var string
     * @ORM\Column(type="string", name="debtor_full_name")
     */
    private $debtorFullName;

    /**
     * Порядок представления заявок
     * @var string
     * @ORM\Column(type="string", name="procedure_info_applications")
     */
    private $procedure_info_applications;

    /**
     * Место представления заявок
     * @var string
     * @ORM\Column(type="string", name="procedure_info_place")
     */
    private $procedure_info_place;

    /**
     * Место проведения торгов
     * @var string
     * @ORM\Column(type="string", name="procedure_info_location")
     */
    private $procedure_info_location;

    /**
     * Название лота
     * @var string
     * @ORM\Column(type="string", name="lot_name")
     */
    private $lotName;

    /**
     * Название лота
     * @var string
     * @ORM\Column(type="string", name="text_notification")
     */
    private $textNotification;

    /**
     * @var ArrayCollection|Document
     * @ORM\OneToMany(targetEntity="App\Model\Front\Entity\OldProcedure\Document\Document", mappedBy="procedure", orphanRemoval=true, cascade={"persist"})
     */

    private $documents;

    /**
     * @var ArrayCollection|Notice
     * @ORM\OneToMany(targetEntity="App\Model\Front\Entity\OldProcedure\Notice\Notice", mappedBy="procedure", orphanRemoval=true, cascade={"persist"})
     */
    private $notice;

    /**
     * @var ArrayCollection|Protocol
     * @ORM\OneToMany(targetEntity="App\Model\Front\Entity\OldProcedure\Protocols\Protocol", mappedBy="procedure", orphanRemoval=true, cascade={"persist"})
     */
    private $protocols;

    public function __construct(
        Id $id,
    string $idNumber,
    string $tenderBasic,
    string $pricePresentationForm,
    string $organizer,
    string $startDateOfApplications,
    string $closingDateOfApplications,
    string $startTradingDate,
    string $status,
    string $reloadLot,
    string $auctionStep,
    string $start_cost,
    string $nds,
    string $deposit_amount,
    string $arrestedPropertyType,
    string $additionalObjectCharacteristics,
    string $region,
    string $locationObject,
    string $organizerShortName,
    string $organizerFullName,
    string $organizerContactFullName,
    string $organizerPhone,
    string $organizerEmail,
    string $organizerLegalAddress,
    string $debtorFullName,
    string $procedure_info_applications,
    string $procedure_info_place,
    string $procedure_info_location,
    string $lotName,
    string $textNotification
    ){
     $this->id = $id;
     $this->idNumber = $idNumber;
     $this->tenderBasic = $tenderBasic;
     $this->pricePresentationForm = $pricePresentationForm;
     $this->organizer = $organizer;
     $this->startDateOfApplications = $startDateOfApplications;
     $this->closingDateOfApplications = $closingDateOfApplications;
     $this->startTradingDate = $startTradingDate;
     $this->status = $status;
     $this->reloadLot = $reloadLot;
     $this->auctionStep = $auctionStep;
     $this->start_cost = $start_cost;
     $this->nds = $nds;
     $this->deposit_amount = $deposit_amount;

     $this->arrestedPropertyType = $arrestedPropertyType;
     $this->additionalObjectCharacteristics = $additionalObjectCharacteristics;
     $this->region = $region;
     $this->locationObject = $locationObject;
     $this->organizerShortName = $organizerShortName;
     $this->organizerFullName = $organizerFullName;
     $this->organizerContactFullName = $organizerContactFullName;
     $this->organizerPhone = $organizerPhone;
     $this->organizerEmail = $organizerEmail;

     $this->organizerLegalAddress = $organizerLegalAddress;
     $this->debtorFullName = $debtorFullName;
     $this->procedure_info_applications = $procedure_info_applications;
     $this->procedure_info_place = $procedure_info_place;
     $this->procedure_info_location = $procedure_info_location;
     $this->lotName = $lotName;
     $this->textNotification = $textNotification;
     $this->documents = new ArrayCollection();
     $this->notice = new ArrayCollection();
    }

    /**
     * @return Document|ArrayCollection
     */
    public function getDocuments(){
        return $this->documents;
    }

    /**
     * @return Notice|ArrayCollection
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @return Protocol|ArrayCollection
     */
    public function getProtocols()
    {
        return $this->protocols;
    }


    /**
     * @param Document\Id $id
     * @param string $type
     * @param string $name
     * @param string $fullPath
     */
    public function addDocument(
        \App\Model\Front\Entity\OldProcedure\Document\Id $id,
                                string $type,
                                string $name,
                                string $fullPath
    ){
        $this->documents->add(new Document(
            $id,
            $type,
            $this,
            $name,
            $fullPath
        ));
    }

    /**
     * @param Notice\Id $id
     * @param string $name
     * @param string $text
     */
    public function addNotice(\App\Model\Front\Entity\OldProcedure\Notice\Id $id, string $name, string $text){
        $this->notice->add(new Notice(
            $id,
            $this,
            $name,
            $text
        ));
    }

    /**
     * @param Protocols\Id $id
     * @param string $name
     * @param string $text
     */
    public function addProtocol(\App\Model\Front\Entity\OldProcedure\Protocols\Id $id, string $name, string $text)
    {
        $this->protocols->add(
            new Protocol(
                $id,
                $this,
                $name,
                $text
            )
        );

    }
}