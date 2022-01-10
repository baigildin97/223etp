<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Lot\Bid;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryWidget extends AbstractExtension

{
    public function getFunctions(): array {
        return [
            new TwigFunction('bid_history_category', [$this, 'status'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $status): string {
        return $twig->render('widget/procedure/lot/bid/category.html.twig',[
            'status' => $status
        ]);
    }
}