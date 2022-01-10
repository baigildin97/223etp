<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\Document;

use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Document
 * @package App\Model\Work\Procedure\Entity\Lot\Bid\Document
 * @ORM\Entity()
 * @ORM\Table(name="bid_documents")
 */
class Document
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="bid_id")
     */
    private $id;

    /**
     * @var Status
     * @ORM\Column(type="bid_document_status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string", name="document_name")
     */
    private $documentName;

    /**
     * @var File
     * @ORM\Embedded(class="File")
     */
    private $file;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Bid
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", inversedBy="documents")
     * @ORM\JoinColumn(name="bid_id", referencedColumnName="id", nullable=false)
     */
    private $bid;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $participantIp;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $signedAt;

    /**
     * Document constructor.
     * @param Id $id
     * @param Status $status
     * @param File $file
     * @param \DateTimeImmutable $createdAt
     * @param Bid $bid
     * @param string $participantIp
     * @param string $documentName
     */
    public function __construct(
        Id $id,
        Status $status,
        File $file,
        \DateTimeImmutable $createdAt,
        Bid $bid,
        string $participantIp,
        string $documentName
    ){
        $this->id = $id;
        $this->status = $status;
        $this->file = $file;
        $this->createdAt = $createdAt;
        $this->bid = $bid;
        $this->participantIp = $participantIp;
        $this->documentName = $documentName;
    }

    /**
     * @return string
     */
    public function getParticipantIp(): string
    {
        return $this->participantIp;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Bid
     */
    public function getBid(): Bid
    {
        return $this->bid;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }


    public function archived(): void {
        if ($this->status->isArchived()){
            throw new \DomainException('Документ уже удален.');
        }
        $this->status = Status::archived();
    }

    public function sign(string $sign, string $participantIp): void {
        if ($this->status->isSigned()){
            throw new \DomainException('Документ уже подписан.');
        }
        $this->file->addSign($sign);
        $this->participantIp = $participantIp;
        $this->status = Status::signed();
        $this->signedAt = new \DateTimeImmutable();
    }

}