<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment;



use App\Helpers\FormatMoney;
use Money\Currency;
use Money\Money;

class DetailView
{
    public $id;
    public $invoice_number;
    public $available_amount;
    public $blocked_amount;
    public $withdrawal_amount;
    public $created_at;
    public $profile_id;
    public $user_full_name;

    /**
     * @return bool
     */
    public function isNegativeAvailableAmount(): bool{
        list($currency, $amount) = explode(' ', $this->available_amount, 2);
        $money = new Money((int) $amount, new Currency($currency));
        return $money->isNegative();
    }
}