<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Certificate;


use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use App\Model\User\Entity\Certificate\CertResetToken;

/**
 * Class Certificate
 * @ORM\Entity()
 * @ORM\Table(name="certificates")
 */
class Certificate
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="certificate_id")
     */
    private $id;

    /**
     * @var SubjectName
     * @ORM\Embedded(class="SubjectName")
     */
    private $subjectName;

    /**
     * @var IssuerName
     * @ORM\Embedded(class="IssuerName")
     */
    private $issuerName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $thumbprint;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=false, name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="archived_date")
     */
    private $archivedDate;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=false, name="valid_from")
     */
    private $validFrom;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=false, name="valid_to")
     */
    private $validTo;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User", inversedBy="certificates")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Status
     * @ORM\Column(type="certificate_status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="text", options={"default":"sign"})
     */
    private $sign;

    /**
     * @var CertResetToken
     * @ORM\Embedded(class="CertResetToken", columnPrefix="confirm_token_")
     */
    private $confirmToken;

    /**
     * Certificate constructor.
     * @param Id $id
     * @param User $user
     * @param string $thumbprint
     * @param SubjectName $subjectName
     * @param IssuerName $issuerName
     * @param \DateTimeImmutable $validFrom
     * @param \DateTimeImmutable $validTo
     * @param \DateTimeImmutable $createdAt
     * @param Status $status
     * @param string $sign
     * @param \App\Model\User\Entity\Certificate\CertResetToken $confirmToken
     */
    public function __construct(
        Id $id,
        User $user,
        string $thumbprint,
        SubjectName $subjectName,
        IssuerName $issuerName,
        \DateTimeImmutable $validFrom,
        \DateTimeImmutable $validTo,
        \DateTimeImmutable $createdAt,
        Status $status,
        string $sign,
        CertResetToken $confirmToken = null
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->thumbprint = $thumbprint;
        $this->subjectName = $subjectName;
        $this->issuerName = $issuerName;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->createdAt = $createdAt;
        $this->status = $status;
        $this->sign = $sign;
        $this->confirmToken = $confirmToken;
    }

    /**
     * @return Id
     */
    public function getId(): Id{
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getArchivedDate(){
        return $this->archivedDate;
    }

    /**
     * @return \App\Model\User\Entity\Certificate\CertResetToken
     */
    public function getConfirmToken(): \App\Model\User\Entity\Certificate\CertResetToken
    {
        return $this->confirmToken;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable{
        return $this->createdAt;
    }

    /**
     * @return IssuerName
     */
    public function getIssuerName(): IssuerName {
        return $this->issuerName;
    }

    /**
     * @return SubjectName
     */
    public function getSubjectName(): SubjectName {
        return $this->subjectName;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getThumbprint(): string{
        return $this->thumbprint;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValidFrom(): \DateTimeImmutable{
        return $this->validFrom;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getValidTo(): \DateTimeImmutable{
        return $this->validTo;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param Certificate $certificate
     * @return bool
     */
    public function isForCertificate(self $certificate): bool {
        return $this->getThumbprint() === $certificate->getThumbprint();
    }


    public function archived(): void {
        $this->status = Status::archived();
    }

    public function isLegalEntity(): bool {
        //Юридическое лицо
        return !empty($this->getSubjectName()->getOgrn());
    }

    public function isIndividualEntrepreneur(): bool {
        //Индивидуаьный предриниматель
        return !empty($this->getSubjectName()->getOgrnIp());
    }

    public function isIndividual(): bool {
        //Физическое лицо
        return empty($this->getSubjectName()->getOgrnIp()) && empty($this->getSubjectName()->getOgrnIp());
    }

    public function confirmByToken(): void
    {
        $this->confirmToken = null;
        $this->status = Status::active();
    }
}
