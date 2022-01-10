<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use App\Model\User\Entity\Profile\Payment\Transaction\Transaction;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use App\Model\Work\Procedure\Entity\Lot\Bid\Document;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Bid
 * @package App\Model\Work\Procedure\Entity\Bid
 * @ORM\Entity()
 * @ORM\Table(name="bids")
 */
class Bid
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="bid_id")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @var Transaction|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Payment\Transaction\Transaction", mappedBy="bid", cascade={"all"})
     */
    private $transaction;

    /**
     * @var Status
     * @ORM\Column(type="bid_status")
     */
    private $status;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="bids")
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id", nullable=false)
     */
    private $participant;

    /**
     * @var Lot
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Lot", inversedBy="bids")
     * @ORM\JoinColumn(name="lot_id", referencedColumnName="id")
     */
    private $lot;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $place;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $participantIp;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $organizerIp;

    /**
     * @var string
     * @ORM\Column(type="text", name="organizer_comment", nullable=true)
     */
    private $organizerComment;

    /**
     * @var string
     * @ORM\Column(type="text", name="organizer_comment_sign", nullable=true)
     */
    private $organizerCommentSign;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $organizerIpCreatedAt;

    /**
     * @var XmlDocument\XmlDocument|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\XmlDocument", mappedBy="bid", cascade={"all"})
     */
    private $xmlDocuments;

    /**
     * @var Document\Document|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Document\Document", mappedBy="bid", cascade={"all"})
     */
    private $documents;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmXml;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $confirmSign;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $confirmHash;

    /**
     * @var Requisite
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Requisite\Requisite", inversedBy="bids")
     * @ORM\JoinColumn(name="requisite_id", referencedColumnName="id", nullable=true)
     */
    private $requisite;

    /**
     * @var SubscribeTariff
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff", inversedBy="bids")
     * @ORM\JoinColumn(name="subscribe_tariff_id", referencedColumnName="id", nullable=true)
     */
    private $subscribeTariff;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $signedAt;

    /**
     * Bid constructor.
     * @param Id $id
     * @param int $number
     * @param Status $status
     * @param Profile $participant
     * @param Lot $lot
     * @param string $participantIp
     * @param \DateTimeImmutable $createdAt
     * @param Requisite $requisite
     * @param SubscribeTariff $subscribeTariff
     */
    public function __construct(
        Id $id,
        int $number,
        Status $status,
        Profile $participant,
        Lot $lot,
        string $participantIp,
        \DateTimeImmutable $createdAt,
        Requisite $requisite,
        SubscribeTariff $subscribeTariff
    ){
        $this->id = $id;
        $this->number = $number;
        $this->status = $status;
        $this->participant = $participant;
        $this->lot = $lot;
        $this->participantIp = $participantIp;
        $this->createdAt = $createdAt;
        $this->documents = new ArrayCollection();
        $this->xmlDocuments = new ArrayCollection();
        $this->requisite = $requisite;
        $this->subscribeTariff = $subscribeTariff;
    }


    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Transaction|ArrayCollection
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return Lot
     */
    public function getLot(): Lot
    {
        return $this->lot;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
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
    public function getOrganizerIp(): string
    {
        return $this->organizerIp;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getOrganizerIpCreatedAt(): \DateTimeImmutable
    {
        return $this->organizerIpCreatedAt;
    }

    /**
     * @return Profile
     */
    public function getParticipant(): Profile
    {
        return $this->participant;
    }

    /**
     * @return string
     */
    public function getParticipantIp(): string
    {
        return $this->participantIp;
    }

    /**
     * @return int|null
     */
    public function getPlace(): ? int
    {
        return $this->place;
    }


    /**
     * @return XmlDocument\XmlDocument|ArrayCollection
     */
    public function getXmlDocuments()
    {
        return $this->xmlDocuments;
    }

    /**
     * @return string| null
     */
    public function getConfirmXml(): ? string
    {
        return $this->confirmXml;
    }

    /**
     * @return SubscribeTariff
     */
    public function getSubscribeTariff(): SubscribeTariff
    {
        return $this->subscribeTariff;
    }

    /**
     * @param Document\Id $id
     * @param Document\Status $status
     * @param Document\File $file
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @param string $documentName
     */
    public function addDocument(
        Document\Id $id,
        Document\Status $status,
        Document\File $file,
        \DateTimeImmutable $createdAt,
        string $clientIp,
        string $documentName
    ): void {
        $this->documents->add(
            new Document\Document($id,$status,$file, $createdAt,$this,$clientIp, $documentName)
        );
    }

    /**
     * @param XmlDocument\Id $id
     * @param XmlDocument\Status $status
     * @param string $xml
     * @param string $hash
     * @param string $sign
     * @param XmlDocument\Category $category
     * @param \DateTimeImmutable $signedAt
     */
    public function sign(
        XmlDocument\Id $id,
        XmlDocument\Status $status,
        string $xml,
        string $hash,
        string $sign,
        XmlDocument\Category $category,
        \DateTimeImmutable $signedAt
    ): void {
        if (!$this->status->isNew()){
            throw new \DomainException('Запрос на отправку заявки отклонен.');
        }


        foreach ($this->documents as $document){
            if ($document->getStatus()->isNew()){
                throw new \DomainException('Подписаны не все документы заявки.');
            }
        }

        $this->xmlDocuments->add(
            new XmlDocument\XmlDocument(
                $id,
                $status,
                $xml,
                $hash,
                $sign,
                $this,
                $category,
                $signedAt
            )
        );
        $this->signedAt = $signedAt;
        $this->status = Status::sent();
    }

    /**
     * Подписание организатором
     * @param XmlDocument\Id $id
     * @param XmlDocument\Status $status
     * @param string $xml
     * @param string $hash
     * @param string $sign
     * @param XmlDocument\Category $category
     * @param \DateTimeImmutable $signedAt
     * @param string $clientIp
     */
    public function signOrganizer(
        XmlDocument\Id $id,
        XmlDocument\Status $status,
        string $xml,
        string $hash,
        string $sign,
        XmlDocument\Category $category,
        \DateTimeImmutable $signedAt,
        string $clientIp
    ): void {

        $this->organizerComment = $xml;
        $this->organizerCommentSign = $sign;
        $this->organizerIp = $clientIp;
        $this->organizerIpCreatedAt = $signedAt;
    }

    /**
     * @param XmlDocument\Id $id
     * @param XmlDocument\Status $status
     * @param string $xml
     * @param string $hash
     * @param string $sign
     * @param XmlDocument\Category $category
     * @param \DateTimeImmutable $signedAt
     */
    public function recall(
        XmlDocument\Id $id,
        XmlDocument\Status $status,
        string $xml,
        string $hash,
        string $sign,
        XmlDocument\Category $category,
        \DateTimeImmutable $signedAt
    ): void {
        // Закрываем отзыв если уже отозван
        if ($this->status->isRecalled()){
            throw new \DomainException('Запрос на отзыв заявки отклонен.');
        }

        // Закрываем отзыв если заявка отклонена
        if ($this->status->isReject()){
            throw new \DomainException('Запрос на отзыв заявки отклонен.');
        }

        $this->xmlDocuments->add(
            new XmlDocument\XmlDocument(
                $id,
                $status,
                $xml,
                $hash,
                $sign,
                $this,
                $category,
                $signedAt
            )
        );
        $this->status = Status::recalled();
    }

    public function confirm(string $xml, string $sign, string $hash): void {
        $this->confirmXml = $xml;
        $this->confirmSign = $sign;
        $this->confirmHash = $hash;
    }

    public function setPlace(int $place): void {
        $this->place = $place;
    }


    /**
     * @return string|null
     */
    public function getOrganizerCommentSign(): ? string
    {
        return $this->organizerCommentSign;
    }

    public function approve(): void{
        if (!$this->status->isSent()){
            throw new \DomainException('Заявка уже обработана.');
        }
        $this->status = Status::approved();
    }

    /**
     * @param string $organizerComment
     */
    public function reject(string $organizerComment): void{
        if (!$this->status->isSent()){
            throw new \DomainException('Заявка уже обработана.');
        }
        $this->organizerComment = $organizerComment;
        $this->status = Status::reject();
    }

    public function checkChargeback(): bool
    {
        if ($this->transaction->count() !== 2){
            return false;
        }

        foreach ($this->transaction as $transaction) {
            if ($transaction->getType()->isSubtract()){
                return true;
            }
        }

        return false;
    }

}