<?php
declare(strict_types=1);
namespace App\Widget;

use App\Helpers\FormatMoney;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DecimalMoney extends AbstractExtension
{
    private $formatMoney;

    public function __construct(FormatMoney $formatMoney)
    {
        $this->formatMoney = $formatMoney;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('decimalMoney', [$this, 'decimalMoney'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function decimalMoney(Environment $twig, string $money): string {
        $money = $this->formatMoney->decimal($money);
        return $twig->render('widget/decimal_money.html.twig',[
            'money' => $money
        ]);
    }
}