<?php
declare(strict_types=1);
namespace App\Widget;

use App\Helpers\FormatMoney;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MoneyWidget extends AbstractExtension
{
    private $formatMoney;

    public function __construct(FormatMoney $formatMoney)
    {
        $this->formatMoney = $formatMoney;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('money', [$this, 'money'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function money(Environment $twig, string $money): string {
        $money = $this->formatMoney->currencyAsSymbol($money);
        return $twig->render('widget/money.html.twig',[
            'money' => $money
        ]);
    }
}