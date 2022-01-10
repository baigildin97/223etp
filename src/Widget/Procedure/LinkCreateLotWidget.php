<?php
declare(strict_types=1);
namespace App\Widget\Procedure;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkCreateLotWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('linkCreate', [$this, 'linkCreate'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function linkCreate(Environment $twig, string $procedureId, string $procedureType): string {
        return $twig->render('widget/procedure/link_create_lot.twig',[
            'procedure_id' => $procedureId,
            'procedure_type' => $procedureType
        ]);
    }

}