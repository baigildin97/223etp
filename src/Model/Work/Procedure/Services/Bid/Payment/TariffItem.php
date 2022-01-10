<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid\Payment;


use Money\Money;

class TariffItem
{
    public $money;
    public $percent;

    public function __construct(Money $money, float $percent){
        $this->money = $money;
        $this->percent = $percent;
    }


}
