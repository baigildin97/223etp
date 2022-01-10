<?php
declare(strict_types=1);
namespace App\Widget\History;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusViewWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('statusView', [$this, 'status'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $status, string $statusTactic, string $type): string {
        return $twig->render('widget/history/status_view.html.twig',[
            'status' => $status,
            'statusTactic' => $statusTactic,
            'type' => $type
        ]);
    }
}