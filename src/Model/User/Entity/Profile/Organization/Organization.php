<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Organization;

use App\Model\User\Entity\Profile\Profile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="profile_organizations")
 */
class Organization
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="profile_organization_id")
     */
    private $id;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="organization")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
    private $profile;

    /**
     * @var string
     * @ORM\Column(type="string", length=12, nullable=false)
     */
    private $inn;

//    /**
//     * @var string
//     * @ORM\Column(type="string", length=20, nullable=false)
//     */
//    private $snils;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factCountry;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factRegion;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factCity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factIndex;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factStreet;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $factHouse;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalCountry;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalRegion;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalCity;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalIndex;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalStreet;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $legalHouse;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="full_title_organization")
     */
    private $fullTitleOrganization;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="short_title_organization")
     */
    private $shortTitleOrganization;

    /**
     * @var string
     * @ORM\Column(type="string", length=9, nullable=true)
     */
    private $kpp;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $ogrn;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true, name="updated_at")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip", nullable=true)
     */
    private $clientIp;

    /**
     * Organization constructor.
     * @param Id $id
     * @param Profile $profile
     * @param string $inn
     * @param string $factCountry
     * @param string $factRegion
     * @param string $factCity
     * @param string $factIndex
     * @param string $factStreet
     * @param string $factHouse
     * @param string $legalCountry
     * @param string $legalRegion
     * @param string $legalCity
     * @param string $legalIndex
     * @param string $legalStreet
     * @param string $legalHouse
     * @param string $shortTitleOrganization
     * @param string $fullTitleOrganization
     * @param string $ogrn
     * @param string $email
     * @param string $snils
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @param string $kpp
     */
    private function __construct(
        Id $id,
        Profile $profile,
        string $inn,
        string $factCountry,
        string $factRegion,
        string $factCity,
        string $factIndex,
        string $factStreet,
        string $factHouse,
        string $legalCountry,
        string $legalRegion,
        string $legalCity,
        string $legalIndex,
        string $legalStreet,
        string $legalHouse,
        ?string $shortTitleOrganization,
        ?string $fullTitleOrganization,
        string $ogrn,
        string $email,
//        string $snils,
        \DateTimeImmutable $createdAt,
        string $clientIp,
        string $kpp
    ) {
        $this->id = $id;
        $this->profile = $profile;
        $this->inn = $inn;
        $this->factCountry = $factCountry;
        $this->factRegion = $factRegion;
        $this->factCity = $factCity;
        $this->factIndex = $factIndex;
        $this->factStreet = $factStreet;
        $this->factHouse = $factHouse;
        $this->legalCountry = $legalCountry;
        $this->legalRegion = $legalRegion;
        $this->legalCity = $legalCity;
        $this->legalIndex = $legalIndex;
        $this->legalStreet = $legalStreet;
        $this->legalHouse = $legalHouse;
        $this->shortTitleOrganization = $shortTitleOrganization;
        $this->fullTitleOrganization = $fullTitleOrganization;
        $this->ogrn = $ogrn;
        $this->email = $email;
//        $this->snils = $snils;
        $this->createdAt = $createdAt;
        $this->clientIp = $clientIp;
        $this->kpp = $kpp;
    }

    /**
     * @param Id $id
     * @param Profile $profile
     * @param string $inn
     * @param string $ogrnip
     * @param string $email
     * @param string $snils
     * @param string $factCountry
     * @param string $factRegion
     * @param string $factCity
     * @param string $factIndex
     * @param string $factStreet
     * @param string $factHouse
     * @param string $legalCountry
     * @param string $legalRegion
     * @param string $legalCity
     * @param string $legalIndex
     * @param string $legalStreet
     * @param string $legalHouse
     * @param null|string $fullTitleIp
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @return static
     */
    public static function createIndividualEntrepreneur(
        Id $id,
        Profile $profile,
        string $inn,
        string $ogrnip,
        string $email,
//        string $snils,
        string $factCountry,
        string $factRegion,
        string $factCity,
        string $factIndex,
        string $factStreet,
        string $factHouse,
        string $legalCountry,
        string $legalRegion,
        string $legalCity,
        string $legalIndex,
        string $legalStreet,
        string $legalHouse,
        ?string $fullTitleIp,
        \DateTimeImmutable $createdAt,
        string $clientIp
    ): self {

        return new self(
            $id,
            $profile,
            $inn,
            $factCountry,
            $factRegion,
            $factCity,
            $factIndex,
            $factStreet,
            $factHouse,
            $legalCountry,
            $legalRegion,
            $legalCity,
            $legalIndex,
            $legalStreet,
            $legalHouse,
            $fullTitleIp,
            $fullTitleIp,
            $ogrnip,
            $email,
//            $snils,
            $createdAt,
            $clientIp,
            'null',
        );
    }

    /**
     * @param Id $id
     * @param Profile $profile
     * @param string $inn
     * @param string $factCountry
     * @param string $factRegion
     * @param string $factCity
     * @param string $factIndex
     * @param string $factStreet
     * @param string $factHouse
     * @param string $legalCountry
     * @param string $legalRegion
     * @param string $legalCity
     * @param string $legalIndex
     * @param string $legalStreet
     * @param string $legalHouse
     * @param string $shortTitleOrganization
     * @param string $fullTitleOrganization
     * @param string $ogrn
     * @param string $email
     * @param string $kpp
     * @param string $snils
     * @param \DateTimeImmutable $createdAt
     * @param string $clientIp
     * @return static
     */
    public static function createLegalEntity(
        Id $id,
        Profile $profile,
        string $inn,
        string $factCountry,
        string $factRegion,
        string $factCity,
        string $factIndex,
        string $factStreet,
        string $factHouse,
        string $legalCountry,
        string $legalRegion,
        string $legalCity,
        string $legalIndex,
        string $legalStreet,
        string $legalHouse,
        string $shortTitleOrganization,
        string $fullTitleOrganization,
        string $ogrn,
        string $email,
        string $kpp,
        \DateTimeImmutable $createdAt,
        string $clientIp
    ): self {
        $legalEntity = new self(
            $id,
            $profile,
            $inn,
            $factCountry,
            $factRegion,
            $factCity,
            $factIndex,
            $factStreet,
            $factHouse,
            $legalCountry,
            $legalRegion,
            $legalCity,
            $legalIndex,
            $legalStreet,
            $legalHouse,
            $shortTitleOrganization,
            $fullTitleOrganization,
            $ogrn,
            $email,
            $createdAt,
            $clientIp,
            $kpp
        );
        return $legalEntity;
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @return string
     */
    public function getFullTitleOrganization(): string
    {
        return $this->fullTitleOrganization;
    }

    /**
     * @return string
     */
    public function getInn(): string
    {
        return $this->inn;
    }

    /**
     * @return string
     */
    public function getKpp(): string
    {
        return $this->kpp;
    }


    /**
     * @return string
     */
    public function getOgrn(): string
    {
        return $this->ogrn;
    }

    /**
     * @return string
     */
    public function getShortTitleOrganization(): string
    {
        return $this->shortTitleOrganization;
    }

//    /**
//     * @return string
//     */
//    public function getSnils(): string
//    {
//        return $this->snils;
//    }

    public function getId(): Id {
        return $this->id;
    }

    /**
     * @return IncorporationForm
     */
    public function getIncorporationForm(): IncorporationForm
    {
        return $this->incorporationForm;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }
}