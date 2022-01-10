<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Members;


use App\Model\User\Entity\Commission\Commission\Commission;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Members
 * @package App\Model\Profile\Entity\Commission\Members
 * @ORM\Entity()
 * @ORM\Table(name="commission_members")
 */
class Member
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="member_id")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(type="string", name="first_name")
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", name="middle_name")
     */
    private $middleName;

    /**
     * @var string
     * @ORM\Column(type="string", name="positions")
     */
    private $positions;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @var Status
     * @ORM\Column(type="member_status")
     */
    private $status;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

    /**
     * @var Commission
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Commission\Commission\Commission")
     * @ORM\JoinColumn(name="commission_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $commission;

    /**
     * Member constructor.
     * @param Id $id
     * @param \DateTimeImmutable $createdAt
     * @param string $lastName
     * @param string $firstName
     * @param string $middleName
     * @param string $positions
     * @param string $role
     * @param Status $status
     * @param Commission $commission
     */
    public function __construct(
        Id $id,
        \DateTimeImmutable $createdAt,
        string $lastName,
        string $firstName,
        string $middleName,
        string $positions,
        string $role,
        Status $status,
        Commission $commission
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->positions = $positions;
        $this->role = $role;
        $this->status = $status;
        $this->commission = $commission;
    }

    /**
     * @param \DateTimeImmutable $archivedAt
     */
    public function setArchivedAt(\DateTimeImmutable $archivedAt): void {
        $this->archivedAt = $archivedAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getArchivedAt(): \DateTimeImmutable {
        return $this->archivedAt;
    }

    /**
     * @param string $positions
     */
    public function setPositions(string $positions): void {
        $this->positions = $positions;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName(string $middleName): void {
        $this->middleName = $middleName;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole(): string {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getPositions(): string {
        return $this->positions;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->lastName;
    }

    /**
     * @return Id
     */
    public function getId(): Id {
        return $this->id;
    }

    /**
     * @param Id $id
     */
    public function setId(Id $id): void {
        $this->id = $id;
    }

    /**
     * @param \DateTimeImmutable $createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): void {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void {
        $this->status = $status;
    }

    /**
     * @return Commission
     */
    public function getCommission(): Commission {
        return $this->commission;
    }

    /**
     * @param Commission $commission
     */
    public function setCommission(Commission $commission): void {
        $this->commission = $commission;
    }


    public function archived(): void {
        if ($this->status->isArchive()){
            throw new \DomainException('Member not archived.');
        }

        $this->status = Status::archived();
    }

    public function edit(
        string $lastName,
        string $firstName,
        string $middleName,
        string $positions,
        string $role,
        Status $status
    ): void {
        if ($this->status->isArchive()){
            throw new \DomainException('Member not changed.');
        }
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->positions = $positions;
        $this->role = $role;
        $this->status = $status;
    }
}