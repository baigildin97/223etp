<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;

use App\Model\User\Entity\Profile\Payment\Payment;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * Class Transaction
 * @package App\Model\User\Entity\Profile\Payment\Transaction
 * @ORM\Entity()
 * @ORM\Table(name="payment_transactions")
 */
class Transaction
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="payment_transaction_id")
     */
    private $id;

    /**
     * @var Bid
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", inversedBy="transaction")
     * @ORM\JoinColumn(name="bid_id", referencedColumnName="id", nullable=true)
     */
    private $bid;

    /**
     * @var IdNumber
     * @ORM\Column(type="payment_transaction_id_number")
     */
    private $idNumber;

    /**
     * @var TransactionType
     * @ORM\Column(type="transaction_type", name="type", nullable=false)
     */
    private $type;

    /**
     * @var Status
     * @ORM\Column(type="transaction_status_type")
     */
    private $status;

    /**
     * @var Money
     * @ORM\Column(type="money")
     */
    public $money;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Payment\Payment", cascade={"persist"})
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $xml;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @var Requisite
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Requisite\Requisite")
     * @ORM\JoinColumn(name="requisite_id", referencedColumnName="id")
     */
    private $requisite;


    private function __construct(
        Id $id,
        string $idNumber,
        TransactionType $type,
        Status $status,
        Money $money,
        Payment $payment,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->idNumber = $idNumber;
        $this->type = $type;
        $this->status = $status;
        $this->money = $money;
        $this->payment = $payment;
        $this->createdAt = $createdAt;
    }

    public static function deposit(
        Id $id,
        int $idNumber,
        TransactionType $type,
        Status $status,
        Money $money,
        Payment $payment,
        Requisite $requisite,
        \DateTimeImmutable $createdAt
    ): self{

        $me = new self($id, $idNumber, $type, $status, $money, $payment, $createdAt);
        $me->requisite = $requisite;
        return $me;
    }

    public static function withdraw(
        Id $id,
        TransactionType $type,
        string $idNumber,
        Status $status,
        Money $money,
        Payment $payment,
        Requisite $requisite,
        string $xml,
        string $hash,
        string $sign,
        \DateTimeImmutable $createdAt
    ): self {
        $me = new self($id, $idNumber, $type, $status, $money, $payment, $createdAt);
        $me->requisite = $requisite;
        $me->xml = $xml;
        $me->hash = $hash;
        $me->sign = $sign;
        return $me;
    }

    public static function blocking(
        Id $id,
        TransactionType $type,
        IdNumber $idNumber,
        Status $status,
        Money $money,
        Payment $payment,
        \DateTimeImmutable $createdAt
    ): self {
        $me = new self($id, $idNumber, $type, $status, $money, $payment, $createdAt);
        return $me;
    }

    /**
     * @param Bid $idBid
     */
    public function attachedBid(Bid $idBid): void{
        $this->bid = $idBid;
    }

    /**
     * @param Id $id
     * @param TransactionType $type
     * @param IdNumber $idNumber
     * @param Status $status
     * @param Money $money
     * @param Payment $payment
     * @param \DateTimeImmutable $createdAt
     * @return Transaction
     */
    public static function unBlocking(  Id $id,
                                        TransactionType $type,
                                        IdNumber $idNumber,
                                        Status $status,
                                        Money $money,
                                        Payment $payment,
                                        \DateTimeImmutable $createdAt)
    {

        $me = new self($id, $idNumber, $type, $status, $money, $payment, $createdAt);
        return $me;
    }

    /**
     * @param Id $id
     * @param TransactionType $type
     * @param IdNumber $idNumber
     * @param Status $status
     * @param Money $money
     * @param Payment $payment
     * @param \DateTimeImmutable $createdAt
     * @return Transaction
     */
    public static function subtract( Id $id,
                                     TransactionType $type,
                                     Bid $bid,
                                     IdNumber $idNumber,
                                     Status $status,
                                     Money $money,
                                     Payment $payment,
                                     \DateTimeImmutable $createdAt)
    {
        $me = new self($id, $idNumber, $type, $status, $money, $payment, $createdAt);
        $me->bid = $bid;
        return $me;
    }

    /**
     * @return Id
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

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @return TransactionType
     */
    public function getType(): TransactionType
    {
        return $this->type;
    }

    /**
     * @return IdNumber
     */
    public function getIdNumber(): IdNumber
    {
        return $this->idNumber;
    }


    public function cancel(): void {
        if (!$this->status->isPending()){
            throw new \DomainException('You cannot cancel the transaction.');
        }

        if ($this->type->isBlocking() || $this->type->isUnBlocking()){
            throw new \DomainException('You cannot cancel the transaction.');
        }

        if ($this->type->isWithdraw()){
            $this->payment->subtractWithdrawMoney($this->money);
            $this->payment->addAvailableMoney($this->money);
        }


        $this->status = Status::archived();
    }

    public function confirm(): void {
        if (!$this->status->isPending()){
            throw new \DomainException('You cannot confirm the transaction.');
        }

        if ($this->type->isDeposit()){
            $this->payment->addAvailableMoney($this->money);
        }

        if ($this->type->isWithdraw()){
            $this->payment->subtractWithdrawMoney($this->money);
        }

        $this->updatedAt = new \DateTimeImmutable();
        $this->status = Status::completed();
    }

}
