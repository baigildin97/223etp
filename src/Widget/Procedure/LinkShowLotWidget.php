<?php
declare(strict_types=1);
namespace App\Widget\Procedure;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkShowLotWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('linkShowLot', [$this, 'linkShowLot'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function linkShowLot(Environment $twig, string $lot_id, string $procedure_type, string $lot_name): string {
        return $twig->render('widget/procedure/link_show_lot.html.twig',[
            'lot_id' => $lot_id,
            'lot_name' => $lot_name,
            'procedure_type' => $procedure_type
        ]);
    }

}