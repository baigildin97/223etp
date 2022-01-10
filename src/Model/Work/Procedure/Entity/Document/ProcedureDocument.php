<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Entity\Document;

use App\Model\Work\Procedure\Entity\Procedure;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class File
 * @package App\Model\Work\Procedure\Entity\Files\ProcedureFiles
 * @ORM\Entity()
 * @ORM\Table(name="procedure_documents", indexes={
 *     @ORM\Index(columns={"created_at"})
 *     })
 */
class ProcedureDocument
{
    /**
     * @var Id
     * @ORM\Column(type="procedure_document_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var Procedure
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Procedure", inversedBy="documents")
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id", nullable=false)
     */
    private $procedure;

    /**
     * @var Status
     * @ORM\Column(type="procedure_document_status_type")
     */
    private $status;

    /**
     * @var FileType
     * @ORM\Column(type="procedure_document_type")
     */
    public $fileType;

    /**
     * @var File
     * @ORM\Embedded(class="File")
     */
    public $file;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    private $clientIp;

    /**
     * ProcedureFile constructor.
     * @param Id $id
     * @param Procedure $procedure
     * @param File $file
     * @param \DateTimeImmutable $createdAt
     * @param Status $status
     * @param FileType $fileType
     */
    public function __construct(
        Id $id,
        Procedure $procedure,
        File $file,
        \DateTimeImmutable $createdAt,
        Status $status,
        FileType $fileType
    )
    {
        $this->id = $id;
        $this->procedure = $procedure;
        $this->file = $file;
        $this->createdAt = $createdAt;
        $this->status = $status;
        $this->fileType = $fileType;
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
     * @return Procedure
     */
    public function getProfile(): Procedure
    {
        return $this->procedure;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param Id $id
     */
    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    /**
     * @param Procedure $procedure
     */
    public function setProcedure(Procedure $procedure): void
    {
        $this->procedure = $procedure;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $sign
     * @param string $clientIp
     * @param \DateTimeImmutable $signedAt
     */
    public function sign(string $sign, string $clientIp, \DateTimeImmutable $signedAt): void
    {
        if ($this->status->isSigned()) {
            throw new \DomainException('Документ уже подписан.');
        }

        $this->file->addSign($sign, $signedAt);
        $this->clientIp = $clientIp;
        $this->status = Status::signed();
    }

    public function archived(): void
    {
        if ($this->status->isDeleted()) {
            throw new \DomainException('Не удалось удалить файл.');
        }

        if (!$this->procedure->getStatus()->isNew() and !$this->procedure->getStatus()->isRejected()) {
            throw new \DomainException('Не удалось удалить файл.');
        }


        $this->status = Status::deleted();
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getHash(): string
    {
        return $this->file->getHash();
    }
}