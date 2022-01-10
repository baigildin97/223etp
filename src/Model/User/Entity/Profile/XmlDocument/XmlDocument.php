<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\XmlDocument;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\XmlDocument\History\Action;
use App\Model\User\Entity\Profile\XmlDocument\History\History;
use App\Model\User\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class XmlDocument
 * @package App\Model\User\Entity\Profile\XmlDocument
 * @ORM\Entity()
 * @ORM\Table(name="profile_xml_documents")
 */
class XmlDocument
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="profile_xml_document_id")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $idNumber;

    /**
     * @var Status
     * @ORM\Column(type="profile_xml_document_status")
     */
    private $status;

    /**
     * @var StatusTactic
     * @ORM\Column(type="profile_xml_document_status_tactic")
     */
    private $statusTactic;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="xmlDocuments", cascade={"all"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * @var string
     * @ORM\Column(type="text", name="moderator_comment", nullable=true)
     */
    private $moderatorComment;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="moderator_id", referencedColumnName="id", nullable=true)
     */
    private $moderator;

    /**
     * @var History | ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\XmlDocument\History\History", mappedBy="xmlDocument", cascade={"persist"})
     */
    private $history;

    /**
     * @var TypeStatement
     * @ORM\Column(type="profile_xml_document_statement_type")
     */
    private $type;

    /**
     * Xml constructor.
     * @param Id $id
     * @param int $idNumber
     * @param $status
     * @param string $file
     * @param string $hash
     * @param string $sign
     * @param Profile $profile
     * @param \DateTimeImmutable $createdAt
     * @param StatusTactic $statusTactic
     * @param TypeStatement $typeStatement
     */
    public function __construct(
        Id $id,
        int $idNumber,
        $status,
        string $file,
        string $hash,
        string $sign,
        Profile $profile,
        \DateTimeImmutable $createdAt,
        StatusTactic $statusTactic,
        TypeStatement $typeStatement

    ) {
        $this->id = $id;
        $this->idNumber = $idNumber;
        $this->status = $status;
        $this->file = $file;
        $this->hash = $hash;
        $this->sign = $sign;
        $this->profile = $profile;
        $this->createdAt = $createdAt;
        $this->statusTactic = $statusTactic;
        $this->history = new ArrayCollection();
        $this->type = $typeStatement;
    }

    /**
     * Измения статуса Xml документа
     * @param Status $status
     */
    public function setStatus(Status $status){
        $this->status = $status;
    }

    /**
     * @return TypeStatement
     */
    public function getType(): TypeStatement
    {
        return $this->type;
    }

    /**
     * Измения комментария модератора Xml документа
     * @param string $moderatorComment
     */
    public function setComment(string $moderatorComment){
        if ($this->moderatorComment !== null){
            throw new \DomainException('Moderator comment not null.');
        }
        $this->moderatorComment = $moderatorComment;
    }

    /**
     * @return StatusTactic
     */
    public function getStatusTactic(): StatusTactic
    {
        return $this->statusTactic;
    }

    /**
     * @return User
     */
    public function getModerator(): User
    {
        return $this->moderator;
    }

    /**
     * @return History|ArrayCollection
     */
    public function getHistory()
    {
        return $this->history;
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
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @return string
     */
    public function getModeratorComment(): string
    {
        return $this->moderatorComment;
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
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
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
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * Модератор забирает задачу
     * @param User $moderator
     */
    public function takeOnTask(User $moderator, string $clientIp): void {
        if (!$moderator->getRole()->isModerator() && !$moderator->getRole()->isAdmin()){
            throw new \DomainException('Access denied #1.');
        }

        if ($this->moderator !== null){
            throw new \DomainException('There is already a performer.');
        }
        if (!$this->statusTactic->isPublished()){
            throw new \DomainException('Document not published.');
        }
        if (!$this->status->isSigned()){
            throw new \DomainException('Document not signed.');
        }
        $this->moderator = $moderator;
        $this->statusTactic = StatusTactic::processing();
        $this->history->add(
            new History(
                \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
                Action::processing(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }

    /**
     * @param History\Id $id
     * @param Action $action
     * @param User $user
     * @param \DateTimeImmutable $dateTimeImmutable
     * @param string $clientIp
     */
    public function addHistory(
        \App\Model\User\Entity\Profile\XmlDocument\History\Id $id,
        Action $action,
        User $user,
        \DateTimeImmutable $dateTimeImmutable,
        string $clientIp
    ){
        $this->history->add(
            new History(
                $id,
                $action,
                $this,
                $user,
                $dateTimeImmutable,
                $clientIp
            )
        );
    }

    /**
     * Модератор возвращает задачу
     * @param User $moderator
     * @param string $clientId
     */
    public function returnTheTask(User $moderator, string $clientId): void {
        if (!$moderator->getRole()->isModerator() && !$moderator->getRole()->isAdmin()){
            throw new \DomainException('Access denied. #1');
        }
        if ($this->moderator->getId()->getValue() !== $moderator->getId()->getValue()){
            throw new \DomainException('You are not the executor.');
        }
        if (!$this->statusTactic->isProcessing()){
            throw new \DomainException('The document is not being processed.');
        }
        if (!$this->status->isSigned()){
            throw new \DomainException('Document not signed.');
        }
        $this->moderator = null;
        $this->statusTactic = StatusTactic::published();
        $this->history->add(
            new History(
                \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
                Action::returned(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientId
            )
        );
    }

    /**
     * Модератор одобряет профиль
     * TODO - добавить дату истечения договора с пользователем
     * @param User $moderator
     * @param string $clientIp
     */
    public function approve(User $moderator, string $clientIp): void{
        if (!$moderator->getRole()->isModerator() && !$moderator->getRole()->isAdmin()){
            throw new \DomainException('Access denied.');
        }
        if ($this->moderator->getId()->getValue() !== $moderator->getId()->getValue()){
            throw new \DomainException('You are not the executor.');
        }
        if (!$this->statusTactic->isProcessing()){
            throw new \DomainException('The document is not being processed.');
        }
        if (!$this->status->isSigned()){
            throw new \DomainException('Document not signed.');
        }
        $this->status = Status::approve();
        $this->statusTactic = StatusTactic::approved();
        $this->profile->activate();
        $this->history->add(
            new History(
                \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
                Action::approved(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientIp
            )
        );

        $this->profile->registrationDate();
    }

    /**
     * Модератор отклоняет профиль
     * @param User $moderator
     * @param string $moderatorComment
     * TODO - Добавить проверку на дату истечения договора с пользователем
     * @param string $clientIp
     */
    public function reject(User $moderator, string $moderatorComment, string $clientIp): void{
        if (!$moderator->getRole()->isModerator() && !$moderator->getRole()->isAdmin()){
            throw new \DomainException('Access denied.');
        }

        if ($this->moderator->getId()->getValue() !== $moderator->getId()->getValue()){
            throw new \DomainException('You are not the executor.');
        }

        if (!$this->statusTactic->isProcessing()){
            throw new \DomainException('The document is not being processed.');
        }

        if (!$this->status->isSigned()){
            throw new \DomainException('Document not signed.');
        }
        $this->moderatorComment = $moderatorComment;
        $this->status = Status::rejected();
        $this->statusTactic = StatusTactic::rejected();
        $this->moderator = null;
        $this->profile->reject();
        $this->history->add(
            new History(
                \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
                Action::rejected(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }


    public function recall(string $clientIp): void {
        if (!$this->status->isSigned()) {
            throw new \DomainException('Заявление не подписано.');
        }
        $this->status = Status::recalled();
        $this->statusTactic = StatusTactic::recalled();

        $this->history->add(
            new History(
                \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
                Action::recalled(),
                $this,
                $this->profile->getUser(),
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }
}