<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkApplyBidWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('linkApplyBid', [$this, 'linkApplyBid'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function linkApplyBid(Environment $twig, string $lotId, string $procedureType): string {
        return $twig->render('widget/procedure/lot/link_apply_bid.html.twig',[
            'lot_id' => $lotId,
            'procedure_type' => $procedureType
        ]);
    }

}