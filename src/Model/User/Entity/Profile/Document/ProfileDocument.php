<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Document;

use App\Model\User\Entity\Profile\Profile;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class File
 * @package App\Model\Profile\Entity\Profile\Document
 * @ORM\Entity()
 * @ORM\Table(name="profile_documents", indexes={
 *     @ORM\Index(columns={"created_at"})
 *     })
 */
class ProfileDocument
{
    /**
     * @var Id
     * @ORM\Column(type="profile_document_id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="files")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
    private $profile;

    /**
     * @var FileType
     * @ORM\Column(type="profile_document_type")
     */
    private $fileType;

    /**
     * @var Status
     * @ORM\Column(type="profile_document_status_type")
     */
    private $status;

    /**
     * @var File
     * @ORM\Embedded(class="File")
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    private $clientIp;


    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    public function __construct(Id $id, Profile $profile, FileType $fileType, File $file, \DateTimeImmutable $createdAt, Status $status) {
        $this->id = $id;
        $this->profile = $profile;
        $this->fileType = $fileType;
        $this->file = $file;
        $this->createdAt = $createdAt;
        $this->status = $status;
    }

    /**
     * @return Id
     */
    public function getId(): Id {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile {
        return $this->profile;
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
     * @param Profile $profile
     */
    public function setProfile(Profile $profile): void
    {
        $this->profile = $profile;
    }

    /**
     *
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $sign
     * @param string $clientIp
     */
    public function sign(string $sign, string $clientIp): void
    {
        if (!$this->status->isSigned()) {
            $this->file->addSign($sign);
            $this->status = Status::signed();
            $this->clientIp = $clientIp;
        }
    }

    public function archived(): void {
        $this->status = Status::deleted();
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return FileType
     */
    public function getFileType(): FileType
    {
        return $this->fileType;
    }

    public function getHash(): string {
        return $this->file->getHash();
    }

}