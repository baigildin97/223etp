<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Commission;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Commission
 * @ORM\Entity()
 * @ORM\Table(name="commissions")
 */
class Commission
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="commission_id")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var Status
     * @ORM\Column(type="commission_status")
     */
    private $status;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="archived_at")
     */
    private $archivedAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="changed_at")
     */
    private $changedAt;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
    private $profile;

    /**
     * Commission constructor.
     * @param Id $id
     * @param Status $status
     * @param string $title
     * @param \DateTimeImmutable $createdAt
     * @param Profile $profile
     */
    public function __construct(Id $id, Status $status, string $title, \DateTimeImmutable $createdAt, Profile $profile) {
        $this->id = $id;
        $this->status = $status;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->profile = $profile;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
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
     * @param mixed $archivedAt
     */
    public function setArchivedAt($archivedAt): void
    {
        $this->archivedAt = $archivedAt;
    }

    /**
     * @return mixed
     */
    public function getArchivedAt()
    {
        return $this->archivedAt;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }


    public function archived(): void {
        if ($this->status->isArchive()){
            throw new \DomainException('');
        }

        $this->status = Status::archived();
    }

    /**
     * @param string $title
     * @param Status $status
     * @param \DateTimeImmutable $changedAt
     */
    public function edit(string $title, Status $status, \DateTimeImmutable $changedAt): void {
        if ($this->status->isArchive()){
            throw new \DomainException('');
        }
        $this->title = $title;
        $this->status = $status;
        $this->changedAt = $changedAt;
    }

}