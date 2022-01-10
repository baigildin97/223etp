<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment\Requisite\Filter;

/**
 * Class Filter
 * @package App\ReadModel\Profile\Payment\Requisite\Filter
 */
class Filter
{
    public $paymentId;

    public function __construct(? string $paymentId) {
        $this->paymentId = $paymentId;
    }

    public static function fromPayment(string $paymentId): self {
        return new self($paymentId);
    }
}