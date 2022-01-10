<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\XmlDocument;

use App\Model\User\Entity\User\User;
use App\Model\Work\Procedure\Entity\Procedure;
use App\Model\Work\Procedure\Entity\XmlDocument\History\Action;
use App\Model\Work\Procedure\Entity\XmlDocument\History\History;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class XmlDocument
 * @package App\Model\Work\Procedure\Entity\XmlDocument
 * @ORM\Entity()
 * @ORM\Table(name="procedure_xml_documents")
 */
class XmlDocument
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="procedure_xml_document_id")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $number;

    /**
     * @var Status
     * @ORM\Column(type="procedure_xml_document_status")
     */
    private $status;

    /**
     * @var Type
     * @ORM\Column(type="procedure_xml_document_type")
     */
    private $type;

    /**
     * @var StatusTactic
     * @ORM\Column(type="procedure_xml_document_status_tactic")
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
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $signedAt;

    /**
     * @var Procedure
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Procedure", inversedBy="xmlDocuments", cascade={"all"})
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id")
     */
    private $procedure;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="moderator_id", referencedColumnName="id", nullable=true)
     */
    private $moderator;

    /**
     * @var string
     * @ORM\Column(type="string", name="moderator_comment", nullable=true)
     */
    private $moderatorComment;

    /**
     * @var History
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\XmlDocument\History\History", mappedBy="xmlDocument", cascade={"all"})
     */
    private $history;

    /**
     * Xml constructor.
     * @param Id $id
     * @param int $number
     * @param Status $status
     * @param Type $type
     * @param string $file
     * @param string $hash
     * @param Procedure $procedure
     * @param \DateTimeImmutable $createdAt
     * @param StatusTactic $statusTactic
     */
    public function __construct(
        Id $id,
        int $number,
        Status $status,
        Type $type,
        string $file,
        string $hash,
        Procedure $procedure,
        \DateTimeImmutable $createdAt,
        StatusTactic $statusTactic

    ) {
        $this->id = $id;
        $this->number = $number;
        $this->status = $status;
        $this->type = $type;
        $this->file = $file;
        $this->hash = $hash;
        $this->procedure = $procedure;
        $this->createdAt = $createdAt;
        $this->statusTactic = $statusTactic;
        $this->history = new ArrayCollection();
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
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
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
    public function getModeratorComment(): string
    {
        return $this->moderatorComment;
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
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @return StatusTactic
     */
    public function getStatusTactic(): StatusTactic
    {
        return $this->statusTactic;
    }

    /**
     * @return Type
     */
    public function getType(): Type
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    public function signAndPublished(string $sign, string $hash, \DateTimeImmutable $signedAt): void{
        if (!$this->status->isApproved()){
            throw new \DomainException('Документ не промодерирован.');
        }
        $this->sign = $sign;
        $this->hash = $hash;
        $this->status = Status::signed();
        $this->signedAt = $signedAt;
        $this->procedure->activate();
    }

    public function recall(string $clientIp): void{
        if (!$this->status->isModerate()){
            throw new \DomainException('Извещение уже отозвано.');
        }
        
        $this->status = Status::recalled();
        $this->statusTactic = StatusTactic::recalled();

        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::recalled(),
                $this,
                null,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }

    public function cancelModeratePublished(string $clientIp): void{
        $this->status = Status::cancellingPublication();
        $this->statusTactic = StatusTactic::recalled();

        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::cancelPublished(),
                $this,
                null,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }

    public function approve(User $moderator, string $clientIp): void{
        if (!$this->status->isModerate()){
            throw new \DomainException('Запрос на изменение статуса извещения отклонен.');
        }
        $this->status = Status::approve();
        $this->statusTactic = StatusTactic::approved();
        $this->procedure->moderated();
        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::approved(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }

    /**
     * @param User $moderator
     * @param string $clientIp
     * @param string $comment
     */
    public function reject(User $moderator, string $clientIp, string $comment): void{
        if (!$this->status->isModerate()){
            throw new \DomainException('Запрос на изменение статуса извещения отклонен.');
        }

        $this->moderatorComment = $comment;
        $this->status = Status::rejected();
        $this->statusTactic = StatusTactic::rejected();
        $this->procedure->reject();

        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::rejected(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientIp
            )
        );
    }

    public function addSign(string $sign): void{
        $this->sign = $sign;
        $this->signedAt = new \DateTimeImmutable();
    }

    /**
     * @return History
     */
    public function getHistory(): History
    {
        return $this->history;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getSignedAt(): \DateTimeImmutable
    {
        return $this->signedAt;
    }

    /**
     * @param History\Id $id
     * @param User $user
     * @param Action $action
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     */
    public function addHistory(
        \App\Model\Work\Procedure\Entity\XmlDocument\History\Id $id,
        User $user,
        Action $action,
        \DateTimeImmutable $createdAt,
        string $clientIp
    ): void {
        $this->history->add(
            new History(
                $id,
                $action,
                $this,
                $user,
                $createdAt,
                $clientIp
            )
        );
    }

    /**
     * Модератор забирает задачу
     * @param User $moderator
     * @param string $clientIp
     */
    public function takeOnTask(User $moderator, string $clientIp): void {
        if (!$moderator->getRole()->isModerator() && !$moderator->getRole()->isAdmin()){
            throw new \DomainException('Access denied #1.');
        }
        if ($this->moderator !== null){
            throw new \DomainException('Задача уже находится в обработке.');
        }
        if (!$this->statusTactic->isPublished()){
            throw new \DomainException('Document not published.');
        }
        if (!$this->status->isModerate()){
            throw new \DomainException('Документ не отправлен на модерацию.');
        }
        $this->moderator = $moderator;
        $this->statusTactic = StatusTactic::processing();
        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::processing(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
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
        if (!$this->statusTactic->isProcessing()){
            throw new \DomainException('Документ не обрабатывается.');
        }
        if (!$this->status->isModerate()){
            throw new \DomainException('Документ не находиться на модерации.');
        }
        if ($this->moderator->getId()->getValue() !== $moderator->getId()->getValue()){
            throw new \DomainException('You are not the executor.');
        }
        $this->moderator = null;
        $this->statusTactic = StatusTactic::published();
        $this->history->add(
            new History(
                \App\Model\Work\Procedure\Entity\XmlDocument\History\Id::next(),
                Action::returned(),
                $this,
                $moderator,
                new \DateTimeImmutable(),
                $clientId
            )
        );
    }

}