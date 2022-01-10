<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Profile;


use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Entity\Profile\Document\ProfileDocument;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Organization\Organization;
use App\Model\User\Entity\Profile\Payment\Payment;
use App\Model\User\Entity\Profile\Representative\Passport;
use App\Model\User\Entity\Profile\Representative\Representative;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff;
use App\Model\User\Entity\Profile\XmlDocument\History\Action;
use App\Model\User\Entity\Profile\XmlDocument\IdNumber;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\Profile\File\FileHelper;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\Mapping as ORM;
use App\Model\User\Entity\Profile\Payment\Id as PaymentId;
use App\Model\User\Entity\Profile\Representative\Id as RepresentativeId;
use App\Model\User\Entity\Profile\Organization\Id as OrganizationId;


/**
 * @ORM\Entity()
 * @ORM\Table(name="profile_profiles")
 */
class Profile
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="profile_profile_id")
     */
    private $id;

    /**
     * @var ArrayCollection|ProfileDocument[]
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Document\ProfileDocument", mappedBy="profile", orphanRemoval=true, cascade={"all"})
     */
    private $documents;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="created_at", nullable=false)
     */
    private $createdAt;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Role\Role", inversedBy="profiles")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     */
    private $role;

    /**
     * @var IncorporationForm
     * @ORM\Column(type="profile_organization_incorporation_form")
     */
    private $incorporatedForm;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\User\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Organization
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Organization\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $organization;

    /**
     * @var Representative
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Representative\Representative", cascade={"persist"})
     * @ORM\JoinColumn(name="representative_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $representative;

    /**
     * @var Status
     * @ORM\Column(type="profile_status_type", nullable=false)
     */
    private $status;

    /**
     * @var Certificate
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Certificate\Certificate")
     * @ORM\JoinColumn(name="certificate_id", referencedColumnName="id", nullable=false)
     */
    private $certificate;

    /**
     * @var SubscribeTariff
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff", cascade={"persist"})
     * @ORM\JoinColumn(name="subscribe_tariff_id", referencedColumnName="id", nullable=true)
     */
    private $subscribeTariff;

    /**
     * @var Payment
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Payment\Payment", inversedBy="profile", cascade={"persist"})
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id", nullable=true)
     */
    private $payment;

    /**
     * @var Bid|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", mappedBy="participant", orphanRemoval=true, cascade={"all"})
     */
    private $bids;

    /**
     * @var string
     * @ORM\Column(type="string", name="client_ip")
     */
    private $clientIp;

    /**
     * @var XmlDocument\XmlDocument|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\XmlDocument\XmlDocument", mappedBy="profile", cascade={"all"})
     */
    private $xmlDocuments;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $contractPeriod;

    /**
     * @var string
     * @ORM\Column(type="string", name="web_site", nullable=true)
     */
    private $webSite;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="registration_date", nullable=true)
     */
    private $registrationDate;

    /**
     * Profile constructor.
     * @param Id $id
     * @param Role $role
     * @param IncorporationForm $incorporationForm
     * @param User $user
     * @param \DateTimeImmutable $createdAt
     * @param string|null $webSite
     * @param string $clientIp
     */
    private function __construct(
        Id $id,
        Role $role,
        IncorporationForm $incorporationForm,
        User $user,
        \DateTimeImmutable $createdAt,
        ? string $webSite,
        string $clientIp
    )
    {
        $this->id = $id;
        $this->role = $role;
        $this->incorporatedForm = $incorporationForm;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->documents = new ArrayCollection();
        $this->status = Status::wait();
        $this->bids = new ArrayCollection();
        $this->webSite = $webSite;
        $this->clientIp = $clientIp;
    }

    /**
     * @param Id $id
     * @param Role $role
     * @param string $position
     * @param string $confirmingDocument
     * @param string $phone
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
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
     * @param User $user
     * @param string $webSite
     * @param string $clientIp
     * @return Profile
     */
    public static function createLegalEntity(
        Id $id,
        Role $role,
        string $position,
        string $confirmingDocument,
        string $phone,
        string $firstName,
        string $middleName,
        string $lastName,
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
        string $snils,
        \DateTimeImmutable $createdAt,
        User $user,
        ? string $webSite,
        string $clientIp,
        string $representativeInn
    ){
        $legalEntity = new self(
            $id,
            $role,
            IncorporationForm::legalEntity(),
            $user,
            $createdAt,
            $webSite,
            $clientIp
        );

        $passport = Passport::createLegalEntity($firstName, $middleName, $lastName, $snils);
        $passport->setInn($representativeInn);

        $represent = Representative::createLegalEntity(
            RepresentativeId::next(),
            $legalEntity,
            $position,
            $phone,
            $passport,
            new \DateTimeImmutable(),
            $clientIp
        );
        $represent->setPosition($position);
        $represent->setConfirmingDocument($confirmingDocument);
        $legalEntity->representative = $represent;
        $legalEntity->organization = Organization::createLegalEntity(
            OrganizationId::next(),
            $legalEntity,
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
            $kpp,
            $createdAt,
            $clientIp
        );
        return $legalEntity;
    }

    /**
     * @param Id $id
     * @param Role $role
     * @param string|null $position
     * @param string $phone
     * @param string $firstName
     * @param string $middleName
     * @param string $lastName
     * @param string $inn
     * @param string $fullTitleOrganization
     * @param string $ogrnip
     * @param string $email
     * @param string $snils
     * @param string $passportSeries
     * @param string $passportNumber
     * @param string $issuer
     * @param \DateTime $issuanceDate
     * @param string $citizenship
     * @param string $unitCode
     * @param \DateTime $birthDay
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
     * @param \DateTimeImmutable $createdAt
     * @param User $user
     * @param string $webSite
     * @param string $clientIp
     * @return Profile
     */
    public static function createIndividualEntrepreneur(
        Id $id,
        Role $role,
        ?string $position,
        string $phone,
        string $firstName,
        string $middleName,
        string $lastName,
        string $inn,
        string $fullTitleOrganization,
        string $ogrnip,
        string $email,
        string $snils,
        string $passportSeries,
        string $passportNumber,
        string $issuer,
        \DateTimeImmutable $issuanceDate,
        string $citizenship,
        string $unitCode,
        \DateTimeImmutable $birthDay,
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
        \DateTimeImmutable $createdAt,
        User $user,
        ?string $webSite,
        string $clientIp
    )
    {
        $individualEntrepreneur = new self(
            $id,
            $role,
            IncorporationForm::individualEntrepreneur(),
            $user,
            $createdAt,
            $webSite,
            $clientIp
        );
        $individualEntrepreneurRepresent = Representative::createIndividualEntrepreneur(
            RepresentativeId::next(),
            $individualEntrepreneur,
            $position,
            $phone,
            Passport::createIndividualEntrepreneur(
                $firstName,
                $middleName,
                $lastName,
                $passportSeries,
                $passportNumber,
                $issuer,
                $issuanceDate,
                $citizenship,
                $unitCode,
                $birthDay,
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
                $snils
            ),
            new \DateTimeImmutable(),
            $clientIp
        );
        $individualEntrepreneur->representative = $individualEntrepreneurRepresent;

        $individualEntrepreneur->organization = Organization::createIndividualEntrepreneur(
            OrganizationId::next(),
            $individualEntrepreneur,
            $inn,
            $ogrnip,
            $email,
//            $snils,
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
            $fullTitleOrganization,
            $createdAt,
            $clientIp
        );
        return $individualEntrepreneur;
    }


    public static function createIndividual(
        Id $id,
        Role $role,
        string $phone,
        string $firstName,
        string $middleName,
        string $lastName,
        string $inn,
        string $email,
        string $snils,
        string $passportSeries,
        string $passportNumber,
        string $issuer,
        \DateTimeImmutable $issuanceDate,
        string $citizenship,
        string $unitCode,
        \DateTimeImmutable $birthDay,
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
        \DateTimeImmutable $createdAt,
        User $user,
        ?string $webSite,
        string $clientIp
    )
    {
        $individual = new self(
            $id,
            $role,
            IncorporationForm::individual(),
            $user,
            $createdAt,
            $webSite,
            $clientIp
        );
        $individualRepresent = Representative::createIndividual(
            RepresentativeId::next(),
            $individual,
            $phone,
            Passport::createIndividual(
                $firstName,
                $middleName,
                $lastName,
                $passportSeries,
                $passportNumber,
                $issuer,
                $issuanceDate,
                $citizenship,
                $unitCode,
                $birthDay,
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
                $inn,
                $snils
            ),
            new \DateTimeImmutable(),
            $clientIp
        );
        $individual->representative = $individualRepresent;


        return $individual;
    }

    /**
     * @return mixed
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

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return ProfileDocument[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @return XmlDocument\XmlDocument|ArrayCollection
     */
    public function getXmlDocuments()
    {
        return $this->xmlDocuments;
    }

    public function archived(): void
    {
        $this->status = Status::archived();
    }

    /**
     * @param ProfileDocument $profileDocument
     */
    public function addFile(ProfileDocument $profileDocument): void
    {
        $this->documents->add($profileDocument);
    }

    /**
     * @return Certificate
     */
    public function getCertificate(): Certificate
    {
        return $this->certificate;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function getRepresentative(): Representative
    {
        return $this->representative;
    }

    /**
     * @return IncorporationForm
     */
    public function getIncorporatedForm(): IncorporationForm
    {
        return $this->incorporatedForm;
    }

    /**
     * @return Payment
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @return SubscribeTariff
     */
    public function getSubscribeTariff(): ?SubscribeTariff
    {
        return $this->subscribeTariff;
    }

    //    public function addDocument(Document $document): void {
//        $document->setRepresentative($this->representative);
//        $document->setOrganization($this->organization);
//        $document->setProfile($this);
//
//        $accreditationRequest = new Accreditation(
//            $document, new \DateTimeImmutable(),
//            AccreditationStatus::pending(),
//            AccreditationId::next());
//
//        $document->setAccreditationRequest($accreditationRequest);
//
//        $this->documents->add($document);
//    }

    /**
     * @param SubscribeTariff $subscribeTariff
     * @param \DateTimeImmutable $date
     */
    public function subscribeToTariff(SubscribeTariff $subscribeTariff, \DateTimeImmutable $date): void
    {
        if ($this->subscribeTariff && !$this->subscribeTariff->isExpiredTo($date)) {
            throw new \DomainException("Already connect tariff.");
        }

        $this->subscribeTariff = $subscribeTariff;

    }

    public function attachCertificate(Certificate $certificate): void
    {
        if ($this->user->getId()->getValue() !== $certificate->getUser()->getId()->getValue()) {
            throw new \DomainException('Not access');
        }

        $this->certificate = $certificate;
    }

    /**
     * @param string $invoiceNumber
     */
    public function createPaymentAccount(string $invoiceNumber): void
    {
        if ($this->payment) {
            throw new \DomainException('Payment account has been created.');
        }

        $this->status = Status::active();

        $this->payment = new Payment(
            PaymentId::next(),
            $invoiceNumber,
            $this,
            new \DateTimeImmutable()
        );
    }

    /**
     * @param XmlDocument\Id $id
     * @param int $idNumber
     * @param XmlDocument\Status $status
     * @param string $xml
     * @param string $hash
     * @param string $sign
     * @param \DateTimeImmutable $signedAt
     * @param XmlDocument\StatusTactic $statusTactic
     * @param string $clientIp
     * @param TypeStatement $typeStatement
     */
    public function sign(
        XmlDocument\Id $id,
        int $idNumber,
        XmlDocument\Status $status,
        string $xml,
        string $hash,
        string $sign,
        \DateTimeImmutable $signedAt,
        XmlDocument\StatusTactic $statusTactic,
        string $clientIp,
        TypeStatement $typeStatement
    ): void
    {

        if (!$this->status->isWait() and !$this->status->isReplacingEp()) {
            if (!$this->status->isRejected()) {
                throw new \DomainException('Запрос на отправку заявки отклонен.');
            }
        }

        if ($this->incorporatedForm->isLegalEntity()) {
            $findCategories = FileHelper::$typesLegalEntity;
        } elseif ($this->incorporatedForm->isIndividualEntrepreneur()) {
            $findCategories = FileHelper::$typesIndividualEntrepreneur;
        } elseif ($this->incorporatedForm->isIndividual()) {
            $findCategories = FileHelper::$typesIndividual;
        }

        unset($findCategories[array_search(FileType::OTHER, $findCategories)]);


        foreach ($findCategories as $key) {
            $criteriaWhere = new Criteria();

            $expr = new Comparison('status', Comparison::NEQ, \App\Model\User\Entity\Profile\Document\Status::deleted()->getName());
            $criteriaWhere->where($expr);
            $criteriaWhere->andWhere(new Comparison('fileType', '=', $key));
            if ($this->documents->matching($criteriaWhere)->count() < 1) {
               // throw new \DomainException('Необходимый перечень документов не прикреплен к заявлению на регистрацию.');
            }
        }

        foreach ($this->documents as $document) {
            if ($document->getStatus()->isNew()) {
                throw new \DomainException('Подписаны не все документы профиля.');
            }
        }

        $xmlDoc = new XmlDocument\XmlDocument(
            $id,
            $idNumber,
            $status,
            $xml,
            $hash,
            $sign,
            $this,
            $signedAt,
            $statusTactic,
            $typeStatement
        );
        $xmlDoc->addHistory(
            \App\Model\User\Entity\Profile\XmlDocument\History\Id::next(),
            Action::send(),
            $this->user,
            $signedAt,
            $clientIp

        );
        $this->xmlDocuments->add($xmlDoc);
        $this->status = Status::moderate();
    }

    public function recall(
        XmlDocument\Id $id,
        int $idNumber,
        XmlDocument\Status $status,
        string $xml,
        string $hash,
        string $sign,
        \DateTimeImmutable $signedAt,
        XmlDocument\StatusTactic $statusTactic,
        TypeStatement $typeStatement,
        Status $statusProfile
    ): void {
        if (!$this->status->isModerate()) {
            throw new \DomainException('Ваш профиль не на модерации.');
        }
        $this->xmlDocuments->add(
            new XmlDocument\XmlDocument(
                $id,
                $idNumber,
                $status,
                $xml,
                $hash,
                $sign,
                $this,
                $signedAt,
                $statusTactic,
                $typeStatement
            )
        );

        $this->status = $statusProfile;
    }

    /**
     * @param Certificate $certificate
     */
    public function changeCertificate(Certificate $certificate): void
    {
        if ($this->user->getId()->getValue() !== $certificate->getUser()->getId()->getValue()) {
            throw new \DomainException('Not access');
        }

        $this->certificate = $certificate;
        $this->status = Status::replacingEp();
    }

    /**
     * @param Certificate $certificate
     * @param Representative $representative
     * @param string|null $webSite
     */
    public function resetCertificateIndividual(Certificate $certificate, Representative $representative, ?string $webSite): void
    {
        if ($this->user->getId()->getValue() !== $certificate->getUser()->getId()->getValue()) {
            throw new \DomainException('Not access');
        }
        $this->representative = $representative;
        $this->certificate = $certificate;
        $this->webSite = $webSite;
        $this->status = Status::wait();
    }

    /**
     * @param Certificate $certificate
     * @param Representative $representative
     * @param Organization $organization
     */
    public function resetCertificate(Certificate $certificate, Representative $representative, Organization $organization): void{

        if ($this->user->getId()->getValue() !== $certificate->getUser()->getId()->getValue()) {
            throw new \DomainException('Not access');
        }

        $this->representative = $representative;
        $this->organization = $organization;
        $this->certificate = $certificate;
        $this->status = Status::wait();
    }


    /**
     * @param Certificate $certificate
     * @param Representative $representative
     * @param Organization $organization
     * @param null|string $webSite
     */
    public function changeProfile(Certificate $certificate, Representative $representative, Organization $organization, ?string $webSite): void{

        if ($this->user->getId()->getValue() !== $certificate->getUser()->getId()->getValue()) {
            throw new \DomainException('Not access');
        }

        $this->representative = $representative;
        $this->organization = $organization;
        $this->certificate = $certificate;
        $this->webSite = $webSite;
        $this->status = Status::wait();
    }



    /**
     * Одобрение аккредитация|отклонения|для модератора
     * @param Status $status
     */
    public function changeStatus(Status $status): void
    {
        $this->status = $status;
    }

    /**
     * Активация профиля
     */
    public function activate(): void
    {
        if (!$this->status->isModerate()) {
            throw new \DomainException('Профиль не на модерации.');
        }
        $this->status = Status::active();
    }

    /**
     * Отклонение модерации
     */
    public function reject(): void
    {
        if (!$this->status->isModerate()) {
            throw new \DomainException('Профиль не на модерации.');
        }
        $this->status = Status::rejected();
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getContractPeriod(): \DateTimeImmutable
    {
        return $this->contractPeriod;
    }


    public function checkExpiredContractPeriod(): void{
        if ($this->contractPeriod === null){
            throw new \DomainException("Договор с ЭТП не подписан");
        }

        if ($this->contractPeriod <= new \DateTimeImmutable()){
            throw new \DomainException("Истек срок действия договора с ЭТП");
        }
    }

    /**
     * Подписание контрактка с этп
     * @param \DateTimeImmutable $dateTimeImmutable
     */
    public function signContract(\DateTimeImmutable $dateTimeImmutable): void{
        $this->contractPeriod = $dateTimeImmutable;
    }

    /**
     * Дата регистрации
     */
    public function registrationDate(): void{
        if ($this->registrationDate === null){
            $this->registrationDate = new \DateTimeImmutable();
        }
    }

    /**
     * Расторг контрактка с этп
     */
    public function cancellationContract(): void{
        $this->contractPeriod = null;
        $this->status = Status::wait();
    }

    public function successConfirmCertificate(): void {
        $this->status = Status::active();
    }

    public function failedConfirmCertificate(): void {
        $this->status = Status::rejected();
    }

    public function getFullName(): string {
        return $this->representative->getPassport()->getFullName();
    }
}
