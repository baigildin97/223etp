<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid\Payment;


use Money\Money;

class TariffService
{
    public function getPercent(Money $money): TariffItem {
        $level1 = Money::RUB(250000000);
        $level2 = Money::RUB(500000000);
        $level3 = Money::RUB(1000000000);

        if($money <= $level1){
            list($my_cut, $investorsCut) = $money->allocate([97, 3]);
            return new TariffItem($investorsCut, 3);
        }else if($money > $level1 && $money <= $level2){
            list($my_cut, $investorsCut) = $money->allocate([97.5, 2.5]);
            return new TariffItem($investorsCut, 2.5);
        }else if($money > $level2 && $money <= $level3){
            list($my_cut, $investorsCut) = $money->allocate([98, 2]);
            return new TariffItem($investorsCut, 2);
        }else if($money > $level3){
            list($my_cut, $investorsCut) = $money->allocate([99, 1]);
            return new TariffItem($investorsCut, 1);
        }else{
            throw new \DomainException('Error');
        }
    }



}
