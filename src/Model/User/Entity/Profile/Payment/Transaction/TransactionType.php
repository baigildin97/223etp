<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction;


use Webmozart\Assert\Assert;

/**
 * Class TransactionType
 * @package App\Model\User\Entity\Profile\Payment\Transaction
 */
class TransactionType
{

    private const DEPOSIT = 'DEPOSIT';
    private const WITHDRAW = 'WITHDRAW';
    private const BLOCKING = 'BLOCKING';
    private const UNBLOCKING = 'UNBLOCKING';
    private const SUBTRACT = 'SUBTRACT';

    /**
     * @var string
     */
    private $value;

    /**
     * TransactionType constructor.
     * @param string $value
     */
    public function __construct(string $value) {
        Assert::oneOf($value,[
            self::DEPOSIT,
            self::WITHDRAW,
            self::BLOCKING,
            self::UNBLOCKING,
            self::SUBTRACT
        ]);
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function deposit(): self {
        return new self(self::DEPOSIT);
    }

    /**
     * @return static
     */
    public static function withdraw(): self {
        return new self(self::WITHDRAW);
    }

    /**
     * @return static
     */
    public static function blocking(): self {
        return new self(self::BLOCKING);
    }

    /**
     * @return static
     */
    public static function unBlocking(): self {
        return new self(self::UNBLOCKING);
    }

    /**
     * @return static
     */
    public static function subtract(): self {
        return new self(self::SUBTRACT);
    }

    /**
     * @param TransactionType $type
     * @return bool
     */
    public function isEqual(self $type): bool {
        return $this->getValue() === $type->getValue();
    }

    /**
     * @return bool
     */
    public function isDeposit(): bool {
        return $this->value === self::DEPOSIT;
    }

    /**
     * @return bool
     */
    public function isWithdraw(): bool {
        return $this->value === self::WITHDRAW;
    }

    /**
     * @return bool
     */
    public function isBlocking(): bool {
        return $this->value === self::BLOCKING;
    }

    /**
     * @return bool
     */
    public function isUnBlocking(): bool {
        return $this->value === self::UNBLOCKING;
    }

    /**
     * @return bool
     */
    public function isSubtract(): bool {
        return $this->value === self::SUBTRACT;
    }

    /**
     * @return string
     */
    public function getValue(): string {
        return $this->value;
    }
}