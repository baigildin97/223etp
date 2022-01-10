<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ArrestedTypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('arrested_type', [$this, 'arrestedType'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function arrestedType(Environment $twig, string $arrestedType): string {
        return $twig->render('widget/procedure/lot/arrested_type.html.twig',[
            'arrested_type' => $arrestedType
        ]);
    }
}
