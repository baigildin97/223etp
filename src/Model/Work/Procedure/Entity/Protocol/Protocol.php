<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;


use App\Model\Work\Procedure\Entity\Procedure;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Protocol
 * @package App\Model\Work\Procedure\Entity\Protocol
 * @ORM\Entity()
 * @ORM\Table(name="protocols")
 */
class Protocol
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="protocol_id")
     */
    private $id;

    /**
     * @var IdNumber
     * @ORM\Column(type="protocol_number_id", name="id_number")
     */
    private $idNumber;

    /**
     * @var Type
     * @ORM\Column(type="protocol_type")
     */
    private $type;

    /**
     * @var Procedure
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Procedure", cascade={"persist"})
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id")
     */
    private $procedure;

    /**
     * @var XmlDocument
     * @ORM\Embedded(class="XmlDocument", columnPrefix="xml_")
     */
    private $xmlDocument;

    /**
     * @var Status
     * @ORM\Column(type="protocol_status")
     */
    private $status;

    /**
     * @var Reason
     * @ORM\Column(type="protocol_reason_type")
     */
    private $reason;

    /**
     * @var string
     * @ORM\Column(type="string", name="organizer_comment")
     */
    private $organizerComment;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * Protocol constructor.
     * @param Id $id
     * @param IdNumber $idNumber
     * @param Type $type
     * @param Procedure $procedure
     * @param XmlDocument $xmlDocument
     * @param Status $status
     * @param \DateTimeImmutable $createdAt
     * @param Reason $reason
     * @param string $organizerComment
     */
    public function __construct(
        Id $id,
        IdNumber $idNumber,
        Type $type,
        Procedure $procedure,
        XmlDocument $xmlDocument,
        Status $status,
        \DateTimeImmutable $createdAt,
        Reason $reason,
        string $organizerComment
    ) {
        $this->id = $id;
        $this->idNumber = $idNumber;
        $this->type = $type;
        $this->procedure = $procedure;
        $this->xmlDocument = $xmlDocument;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->reason = $reason;
        $this->organizerComment = $organizerComment;
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
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return XmlDocument
     */
    public function getXmlDocument(): XmlDocument
    {
        return $this->xmlDocument;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return Procedure
     */
    public function getProcedure(): Procedure
    {
        return $this->procedure;
    }

    /**
     * @return IdNumber
     */
    public function getIdNumber(): IdNumber
    {
        return $this->idNumber;
    }

    /**
     * @return string
     */
    public function getOrganizerComment(): string
    {
        return $this->organizerComment;
    }

    /**
     * @return Reason
     */
    public function getReason(): Reason
    {
        return $this->reason;
    }

}