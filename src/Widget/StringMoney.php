<?php
declare(strict_types=1);
namespace App\Widget;


use App\Helpers\FormatMoney;
use App\Widget\PrescriptionDigit\PrescriptionDigit;
use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StringMoney extends AbstractExtension
{
    private $prescriptionDigit;
    private $moneyFormatter;

    public function __construct(FormatMoney $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('string_money', [$this, 'money'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function money(Environment $twig, string $money): string {
        return $twig->render('widget/string_money.html.twig',[
            'string_money' => $this->moneyFormatter->formatPrescription($money)
        ]);
    }
}