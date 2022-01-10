<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Document;

use App\Model\Work\Procedure\Entity\Lot\Lot;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Document
 * @package App\Model\Work\Procedure\Entity\Lot\Document
 * @ORM\Entity()
 * @ORM\Table(name="lot_documents")
 */
class Document
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="lot_id")
     */
    private $id;

    /**
     * @var Status
     * @ORM\Column(type="lot_document_status")
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
     * @var Lot
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Lot", inversedBy="documents")
     * @ORM\JoinColumn(name="lot_id", referencedColumnName="id", nullable=false)
     */
    private $lot;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $participantIp;

    /**
     * Document constructor.
     * @param Id $id
     * @param Status $status
     * @param File $file
     * @param \DateTimeImmutable $createdAt
     * @param Lot $bid
     * @param string $participantIp
     * @param string $documentName
     */
    public function __construct(
        Id $id,
        Status $status,
        File $file,
        \DateTimeImmutable $createdAt,
        Lot $lot,
        string $participantIp,
        string $documentName
    ){
        $this->id = $id;
        $this->status = $status;
        $this->file = $file;
        $this->createdAt = $createdAt;
        $this->lot = $lot;
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
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return Lot
     */
    public function getLot(): Lot
    {
        return $this->lot;
    }


    public function archived(): void {
        if (!$this->status->isNew()){
            throw new \DomainException('Не удалось подписать файл.');
        }

        if ($this->status->isArchived()){
            throw new \DomainException('Документ уже удален.');
        }
        $this->status = Status::archived();
    }

    public function sign(string $sign, string $participantIp): void {
        if (!$this->status->isNew()){
            throw new \DomainException('Не удалось подписать файл.');
        }

        if ($this->status->isSigned()){
            throw new \DomainException('Документ уже подписан.');
        }

        $this->file->addSign($sign);
        $this->participantIp = $participantIp;
        $this->status = Status::signed();
    }

    public function getHash(): string {
        return $this->file->getHash();
    }

}