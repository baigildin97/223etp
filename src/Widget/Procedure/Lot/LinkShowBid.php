<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkShowBid extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('linkShowBid', [$this, 'linkShowBid'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function linkShowBid(Environment $twig, string $bidId, string $procedureType, string $bidNumber): string {
        return $twig->render('widget/procedure/lot/link_show_bid.html.twig',[
            'bid_id' => $bidId,
            'procedure_type' => $procedureType,
            'bid_number' => $bidNumber
        ]);
    }

}