<?php
namespace App\Widget\Profile;

use App\Helpers\Filter;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FilterInnWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('filter_inn', [$this, 'status'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $inn): string {
        $inn = Filter::filterInnLegalEntity($inn);

        return $twig->render('widget/profile/filter_inn.html.twig',[
            'inn' => $inn
        ]);
    }
}