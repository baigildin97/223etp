<?php
namespace App\Model\User\Entity\Profile\XmlDocument\History;


use App\Model\User\Entity\Profile\XmlDocument\XmlDocument;
use App\Model\User\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class History
 * @package App\Model\User\Entity\Profile\XmlDocument\History
 * @ORM\Entity()
 * @ORM\Table(name="profile_xml_document_history")
 */
class History
{

    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="profile_xml_document_history_id")
     */
    private $id;

    /**
     * @var Action
     * @ORM\Column(type="profile_xml_document_history_action_type")
     */
    private $action;

    /**
     * @var XmlDocument
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\XmlDocument\XmlDocument", inversedBy="history")
     * @ORM\JoinColumn(name="xml_document_id", referencedColumnName="id")
     */
    private $xmlDocument;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User", cascade={"all"})
     * @ORM\JoinColumn(name="moderator_id", referencedColumnName="id", nullable=true)
     */
    private $moderator;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $clientIp;

    public function __construct(
        Id $id,
        Action $action,
        XmlDocument $xmlDocument,
        ? User $moderator,
        \DateTimeImmutable $createdAt,
        string $clientIp
    ){
        $this->id = $id;
        $this->action = $action;
        $this->xmlDocument = $xmlDocument;
        $this->moderator = $moderator;
        $this->createdAt = $createdAt;
        $this->clientIp = $clientIp;
    }

    /**
     * @return XmlDocument
     */
    public function getXmlDocument(): XmlDocument
    {
        return $this->xmlDocument;
    }

    /**
     * @return User
     */
    public function getModerator(): User
    {
        return $this->moderator;
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
     * @return Action
     */
    public function getAction(): Action
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getClientIp(): string
    {
        return $this->clientIp;
    }

}