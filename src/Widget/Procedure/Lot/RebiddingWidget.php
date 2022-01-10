<?php
declare(strict_types=1);

namespace App\Widget\Procedure\Lot;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RebiddingWidget extends AbstractExtension
{
    public function getFunctions(): array{
        return [
            new TwigFunction('rebidding', [$this, 'rebidding'],
                ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function rebidding(Environment $twig, string $rebidding): string
    {
        return $twig->render('widget/procedure/lot/rebidding.html.twig', [
            'rebidding' => $rebidding
        ]);
    }
}