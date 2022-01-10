<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment\Transaction\Filter;

/**
 * Class Filter
 * @package App\ReadModel\Profile\Payment\Transaction\Filter
 */
class Filter
{
    /**
     * @var string|null
     */
    public $paymentId;

    /**
     * @var array|null
     */
    public $status;

    /**
     * Filter constructor.
     * @param string|null $paymentId
     * @param array|null $status
     */
    public function __construct(? string $paymentId, ? array $status) {
        $this->paymentId = $paymentId;
        $this->status = $status;
    }

    /**
     * @param string $paymentId
     * @return static
     */
    public static function fromPayment(string $paymentId): self{
        return new self($paymentId, null);
    }

    public static function forStatus(array $status): self {
        return new self(null, $status);
    }



}
