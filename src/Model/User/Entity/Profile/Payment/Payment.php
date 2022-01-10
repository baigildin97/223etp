<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Profile\Payment;

use App\Model\User\Entity\Profile\Payment\Transaction\IdNumber;
use App\Model\User\Entity\Profile\Requisite\Id as RequisiteId;
use App\Model\User\Entity\Profile\Requisite\Status as RequisiteStatus;
use App\Model\User\Entity\Profile\Payment\Transaction\Status;
use App\Model\User\Entity\Profile\Payment\Transaction\Transaction;
use App\Model\User\Entity\Profile\Payment\Transaction\TransactionType;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * Class Payment
 * @package App\Model\User\Entity\Profile\Payment
 * @ORM\Entity()
 * @ORM\Table(name="profile_payments")
 */
class Payment
{
    /**
     * @var Id
     * @ORM\Column(type="profile_payment_id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="invoice_number", unique=true)
     */
    private $invoiceNumber;

    /**
     * @var Money
     * @ORM\Column(type="money", name="available_amount")
     */
    private $availableAmount;

    /**
     * @var Money
     * @ORM\Column(type="money", name="blocked_amount")
     */
    private $blockedAmount;

    /**
     * @var Money
     * @ORM\Column(type="money", name="withdrawal_amount")
     */
    private $withdrawalAmount;

    /**
     * @var string
     * @ORM\Column(type="datetime_immutable", name="created_at")
     */
    private $createdAt;

    /**
     * @var Transaction|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Payment\Transaction\Transaction", mappedBy="payment", cascade={"persist"})
     */
    private $transactions;

    /**
     * @var Profile
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * @var Requisite| ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Requisite\Requisite", mappedBy="payment", cascade={"persist"})
     */
    private $requisites;

    /**
     * Payment constructor.
     * @param Id $id
     * @param string $invoiceNumber
     * @param Profile $profile
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(
        Id $id,
        string $invoiceNumber,
        Profile $profile,
        \DateTimeImmutable $createdAt
    )
    {
        $this->id = $id;
        $this->invoiceNumber = $invoiceNumber;
        $this->profile = $profile;
        $this->createdAt = $createdAt;
        $this->availableAmount = new Money(0, new Currency('RUB'));
        $this->withdrawalAmount = new Money(0, new Currency('RUB'));
        $this->blockedAmount = new Money(0, new Currency('RUB'));
        $this->transactions = new ArrayCollection();
        $this->requisites = new ArrayCollection();
    }

    /**
     * @return Money
     */
    public function getBlockedAmount(): Money
    {
        return $this->blockedAmount;
    }

    /**
     * @return Requisite|ArrayCollection
     */
    public function getRequisites()
    {
        return $this->requisites;
    }

    /**
     * @return Money
     */
    public function getWithdrawalAmount(): Money
    {
        return $this->withdrawalAmount;
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
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @return Transaction|ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @return Money
     */
    public function getAvailableAmount(): Money
    {
        return $this->availableAmount;
    }

    /**
     * @param Money $money
     * @param int $idNumber
     * @param Requisite $requisite
     */
    public function deposit(Money $money, int $idNumber, Requisite $requisite): void
    {

        $this->transactions->add(
            Transaction::deposit(
                \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
                $idNumber,
                TransactionType::deposit(),
                Status::pending(),
                $money,
                $this,
                $requisite,
                new \DateTimeImmutable()
            )
        );
    }

    /**
     * @param Money $money
     */
    public function addAvailableMoney(Money $money): void
    {
        $this->availableAmount = $this->availableAmount->add($money);
    }

    /**
     * @param Money $money
     */
    public function subtractWithdrawMoney(Money $money): void
    {
        $this->withdrawalAmount = $this->withdrawalAmount->subtract($money);
        if ($this->withdrawalAmount->isNegative()) {
            throw new \DomainException('Недостаточно средств.');
        }
    }


    public function withdraw(Money $money,
                             string $idNumber,
                             Requisite $requisite,
                             string $xml,
                             string $hash,
                             string $sign,
                             \DateTimeImmutable $createdAt
    ): void
    {
        $this->availableAmount = $this->availableAmount->subtract($money);
        $this->withdrawalAmount = $this->withdrawalAmount->add($money);
        $this->transactions->add(
            Transaction::withdraw(
                \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
                TransactionType::withdraw(),
                $idNumber,
                Status::pending(),
                $money,
                $this,
                $requisite,
                $xml,
                $hash,
                $sign,
                $createdAt
            )
        );
        if ($this->availableAmount->isNegative()) {
            throw new \DomainException('Недостаточно средств.');
        }
    }

    /**
     * @param RequisiteId $id
     * @param string $paymentAccount
     * @param string|null $personalAccount
     * @param string $bankName
     * @param string $bankBik
     * @param string $correspondentAccount
     * @param string|null $bankAddress
     * @param \DateTimeImmutable $createdAt
     */
    public function addRequisite(
        RequisiteId $id,
        string $paymentAccount,
        ?string $personalAccount,
        string $bankName,
        string $bankBik,
        string $correspondentAccount,
        ?string $bankAddress,
        \DateTimeImmutable $createdAt
    ): void {

        foreach ($this->getRequisites() as $requisite){
            if ($requisite->getStatus()->isActive()){
                throw new \DomainException('Доступ запрещен.');
            }
        }

        $this->requisites->add(
            new Requisite(
                $id,
                $paymentAccount,
                $personalAccount,
                $bankName,
                $bankBik,
                $correspondentAccount,
                $bankAddress,
                $this,
                $createdAt,
                RequisiteStatus::active()
            )
        );
    }

    /**
     * @param Money $money
     */
    public function blocking(Money $money): void
    {
        $this->availableAmount = $this->availableAmount->subtract($money);
        $this->blockedAmount = $this->blockedAmount->add($money);
        $this->transactions->add(
            Transaction::blocking(
                \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
                TransactionType::blocking(),
                IdNumber::next(),
                Status::completed(),
                $money,
                $this,
                new \DateTimeImmutable()
            )
        );
        if ($this->availableAmount->isNegative()) {
            throw new \DomainException('Недостаточно средств.');
        }
    }

    /**
     * @param Money $money
     * @param Bid $bid
     */
    public function blockingMoneyBid(Money $money, Bid $bid): void
    {
        $this->availableAmount = $this->availableAmount->subtract($money);
        $this->blockedAmount = $this->blockedAmount->add($money);
        $tr = Transaction::blocking(
            \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
            TransactionType::blocking(),
            IdNumber::next(),
            Status::completed(),
            $money,
            $this,
            new \DateTimeImmutable()
        );
        $tr->attachedBid($bid);
        $this->transactions->add(
            $tr
        );

    }


    /**
     * @param Money $money
     * @param Bid $bid
     */
    public function blockingMoneyBidNegativeBalance(Money $money, Bid $bid): void
    {
        if ($this->availableAmount->isZero()) {
            throw new \DomainException('Недостаточно средств гарантийного обеспечения на балансе Виртуального счета.');
        }

        if ($this->availableAmount->isNegative()) {
            throw new \DomainException('Недостаточно средств гарантийного обеспечения на балансе Виртуального счета.');
        }

        $this->availableAmount = $this->availableAmount->subtract($money);
        $this->blockedAmount = $this->blockedAmount->add($money);
        $tr = Transaction::blocking(
            \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
            TransactionType::blocking(),
            IdNumber::next(),
            Status::completed(),
            $money,
            $this,
            new \DateTimeImmutable()
        );
        $tr->attachedBid($bid);
        $this->transactions->add(
            $tr
        );
    }



    public function subtractBlockingMoney(Money $money, Bid $bid): void
    {
        $this->blockedAmount = $this->blockedAmount->subtract($money);

        $this->transactions->add(
            Transaction::subtract(
                \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
                TransactionType::subtract(),
                $bid,
                IdNumber::next(),
                Status::completed(),
                $money,
                $this,
                new \DateTimeImmutable()
            )
        );
    }


    /**
     * @param Money $money
     */
    public function unBlocking(Money $money): void
    {
        $this->availableAmount = $this->availableAmount->add($money);
        $this->blockedAmount = $this->blockedAmount->subtract($money);

        $this->transactions->add(
            Transaction::unBlocking(
                \App\Model\User\Entity\Profile\Payment\Transaction\Id::next(),
                TransactionType::unBlocking(),
                IdNumber::next(),
                Status::completed(),
                $money,
                $this,
                new \DateTimeImmutable()
            )
        );
    }
}
