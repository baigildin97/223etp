<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Profile\Requisite;


use App\Model\User\Entity\Profile\Payment\Payment;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use \App\Model\User\Entity\Profile\Profile;

/**
 * Class Requisite
 * @ORM\Entity()
 * @ORM\Table(name="profile_requisites")
 */
class Requisite
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="profile_requisite_id", nullable=false)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=20)
     */
    private $paymentAccount;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $bankName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=9)
     */
    private $bankBik;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=20)
     */
    private $correspondentAccount;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="changed_at", nullable=true)
     */
    private $changedAt;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $bankAddress;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $personalAccount;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Status
     * @ORM\Column(type="profile_requisite_status_type")
     */
    private $status;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Payment\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @var Bid
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", mappedBy="requisite")
     */
    private $bids;

    /**
     * Requisite constructor.
     * @param Id $id
     * @param string $paymentAccount
     * @param string|null $personalAccount
     * @param string $bankName
     * @param string $bankBik
     * @param string $correspondentAccount
     * @param string|null $bankAddress
     * @param Payment $payment
     * @param \DateTimeImmutable $createdAt
     * @param Status $status
     */
    public function __construct(
        Id $id,
        string $paymentAccount,
        ?string $personalAccount,
        string $bankName,
        string $bankBik,
        string $correspondentAccount,
        ?string $bankAddress,
        Payment $payment,
        \DateTimeImmutable $createdAt,
        Status $status
    )
    {
        $this->id = $id;
        $this->paymentAccount = $paymentAccount;
        $this->personalAccount = $personalAccount;
        $this->bankName = $bankName;
        $this->bankBik = $bankBik;
        $this->correspondentAccount = $correspondentAccount;
        $this->createdAt = $createdAt;
        $this->bankAddress = $bankAddress;
        $this->payment = $payment;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getBankBik(): string
    {
        return $this->bankBik;
    }

    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bankName;
    }

    /**
     * @return string
     */
    public function getCorrespondentAccount(): string
    {
        return $this->correspondentAccount;
    }

    /**
     * @return string
     */
    public function getPaymentAccount(): string
    {
        return $this->paymentAccount;
    }

    public function getChangedAt(): \DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param string $bankName
     * @param string $bankBik
     * @param string $corrAccount
     * @param string $paymentAccount
     */
    public function update(string $bankName, string $bankBik, string $corrAccount, string $paymentAccount): void
    {
        $this->bankName = $bankName;
        $this->bankBik = $bankBik;
        $this->correspondentAccount = $corrAccount;
        $this->paymentAccount = $paymentAccount;
    }


    public function archived(): void
    {
        if ($this->status->isInactive()) {
            throw new \DomainException('Error');
        }
        $this->status = Status::inactive();
    }


    /**
     * @param string $bankName
     * @param string $bankBik
     * @param string $correspondentAccount
     * @param string $paymentAccount
     * @param string|null $personalAccount
     * @param string|null $bankAddress
     */
    public function edit(string $bankName,
                         string $bankBik,
                         string $correspondentAccount,
                         string $paymentAccount,
                         ?string $personalAccount,
                         ?string $bankAddress
    ): void
    {
        if ($this->status->isInactive()) {
            throw new \DomainException('Error');
        }
        $this->bankName = $bankName;
        $this->bankBik = $bankBik;
        $this->correspondentAccount = $correspondentAccount;
        $this->paymentAccount = $paymentAccount;
        $this->personalAccount = $personalAccount;
        $this->bankAddress = $bankAddress;
    }
}
