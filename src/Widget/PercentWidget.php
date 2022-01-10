<?php
declare(strict_types=1);
namespace App\Widget;

use App\Helpers\FormatPercent;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PercentWidget extends AbstractExtension
{

    public function getFunctions(): array {
        return [
            new TwigFunction('percent', [$this, 'percent'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function percent(Environment $twig, string $percent): string {

        return $twig->render('widget/percent.html.twig',[
            'percent' => FormatPercent::formatViewSymbol($percent)
        ]);
    }
}