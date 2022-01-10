<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Profile\Representative;

use App\Model\User\Entity\Profile\Profile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="profile_representatives")
 */
class Representative
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="profile_representative_id")
     */
    private $id;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="representative")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
    private $profile;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="confirming_document")
     */
    private $confirmingDocument;

    /**
     * @var string
     * @ORM\Column(type="string", length=11)
     */
    private $phone;

    /**
     * @var Passport
     * @ORM\Embedded(class="Passport", columnPrefix="passport_")
     */
    private $passport;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    private $clientIp;

    private function __construct(
        Id $id,
        Profile $profile,
        string $phone,
        Passport $passport,
        \DateTimeImmutable $createdAt,
        string $clientIp
    )
    {
        $this->id = $id;
        $this->profile = $profile;
        $this->phone = $phone;
        $this->passport = $passport;
        $this->createdAt = $createdAt;
        $this->clientIp = $clientIp;
    }

    /**
     * @param Id $id
     * @param Profile $profile
     * @param string $phone
     * @param Passport $passport
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @return Representative
     */
    public static function createIndividual(Id $id,
                                            Profile $profile,
                                            string $phone,
                                            Passport $passport,
                                            \DateTimeImmutable $createdAt,
                                            string $clientIp)
    {
        return new self($id, $profile, $phone, $passport, $createdAt, $clientIp);
    }

    /**
     * @param Id $id
     * @param Profile $profile
     * @param null|string $position
     * @param string $phone
     * @param Passport $passport
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @return Representative
     */
    public static function createIndividualEntrepreneur(Id $id,
                                                        Profile $profile,
                                                        ?string $position,
                                                        string $phone,
                                                        Passport $passport,
                                                        \DateTimeImmutable $createdAt,
                                                        string $clientIp)
    {
        $representative = new self($id, $profile, $phone, $passport, $createdAt, $clientIp);
        $representative->position = $position;
        return $representative;
    }

    /**
     * @param Id $id
     * @param Profile $profile
     * @param string $position
     * @param string $phone
     * @param Passport $passport
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @return Representative
     */
    public static function createLegalEntity(Id $id,
                                             Profile $profile,
                                             string $position,
                                             string $phone,
                                             Passport $passport,
                                             \DateTimeImmutable $createdAt,
                                             string $clientIp)
    {
        $representative = new self($id, $profile, $phone, $passport, $createdAt, $clientIp);
        $representative->position = $position;
        return $representative;
    }


    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getConfirmingDocument(): string
    {
        return $this->confirmingDocument;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @param string $confirmingDocument
     */
    public function setConfirmingDocument(string $confirmingDocument): void
    {
        $this->confirmingDocument = $confirmingDocument;
    }

    /**
     * @return Passport
     */
    public function getPassport(): Passport
    {
        return $this->passport;
    }

    public function update(
        string $position,
        string $phone,
        string $passport
    ): void
    {
        $this->position = $position;
        $this->phone = $phone;
        $this->passport = $passport;
    }


    /**
     * @param string $position
     */
    public function setPosition(string $position): void
    {
        $this->position = $position;
    }


}