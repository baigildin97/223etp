<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot;


use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

class NdsTypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('ndsType', [$this, 'ndsType'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function ndsType(Environment $twig, string $ndsType): string {
        return $twig->render('widget/procedure/lot/nds_type.html.twig',[
            'nds_type' => $ndsType
        ]);
    }
}