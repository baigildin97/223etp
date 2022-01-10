<?php
declare(strict_types=1);
namespace App\Widget\History;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusTacticWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('statusHistoryTactic', [$this, 'status'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $statusTactic): string {
        return $twig->render('widget/history/status_tactic.html.twig',[
            'statusTactic' => $statusTactic
        ]);
    }
}