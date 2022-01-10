<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReloadLotWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('reloadLot', [$this, 'reloadLot'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function reloadLot(Environment $twig, string $reloadLot): string {
        return $twig->render('widget/procedure/lot/reload_lot.html.twig',[
            'reload_lot' => $reloadLot
        ]);
    }
}