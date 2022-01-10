<?php
declare(strict_types=1);
namespace App\Helpers;

use App\Services\PrescriptionDigit\PrescriptionDigit;
use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

/**
 * Class FormatMoney
 * @package App\Helpers
 */
class FormatMoney
{
    /**
     * @var MoneyFormatter
     */
    private $formatterMoney;

    /**
     * @var PrescriptionDigit
     */
    private $prescriptionDigit;

    /**
     * FormatMoney constructor.
     * @param MoneyFormatter $moneyFormatter
     * @param PrescriptionDigit $prescriptionDigit
     */
    public function __construct(MoneyFormatter $moneyFormatter, PrescriptionDigit $prescriptionDigit){
        $this->formatterMoney = $moneyFormatter;
        $this->prescriptionDigit = $prescriptionDigit;
    }

    /**
     * @param string $money
     * @return string
     */
    public function currencyAsSymbol(string $money){
        return $this->formatterMoney->localizedFormatMoney($this->formatMoney($money));
    }

    /**
     * @param string $money
     * @return Money
     */
    public function money(string $money): Money {
        list($currency, $amount) = explode(' ', $money, 2);
        return new Money((int) $amount, new Currency($currency));
    }

    /**
     * @param string $money
     * @return float|int
     */
    public function decimal(string $money){
        return $this->formatterMoney->asFloat($this->formatMoney($money));
    }

    /**
     * @param string $money
     * @return string
     */
    public function formatAmount(string $money){
        return $this->formatterMoney->formatAmount($this->formatMoney($money));
    }

    /**
     * @param string $money
     * @return string|null
     */
    public function formatPrescription(string $money){
        return $this->prescriptionDigit->get($this->formatterMoney->asFloat($this->formatMoney($money)),'ru',true);
    }

    /**
     * @param Money $money
     * @return string
     */
    public function convertMoneyToString(Money $money): string{
        return $this->formatterMoney->localizedFormatMoney($money);
    }

    /**
     * @param string $money
     * @return Money
     */
    private function formatMoney(string $money): Money{
        list($currency, $amount) = explode(' ', $money, 2);
        return new Money((int) $amount, new Currency($currency));
    }

}
