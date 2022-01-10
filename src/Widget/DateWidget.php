<?php
declare(strict_types=1);
namespace App\Widget;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateWidget extends AbstractExtension
{

    public function getFunctions(): array {
        return [
            new TwigFunction('date', [$this, 'date', 'only_date'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }


    public function date(Environment $twig, ?string $date, ?bool $only_date = false): string {

        return $twig->render('widget/date.html.twig',[
            'only_date' => $only_date,
            'date' => $date
        ]);
    }
}