<?php
declare(strict_types=1);
namespace App\Widget\Procedure;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('statusProcedure', [$this, 'status', 'wrapper'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $status, $wrapper = true): string {
        return $twig->render('widget/procedure/status.html.twig',[
            'status' => $status,
			'wrapper' => $wrapper
        ]);
    }
}