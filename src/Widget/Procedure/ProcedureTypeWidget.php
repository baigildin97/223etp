<?php
declare(strict_types=1);
namespace App\Widget\Procedure;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProcedureTypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('procedure_type', [$this, 'procedureType'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function procedureType(Environment $twig, string $status): string {
        return $twig->render('widget/procedure/procedure_type.html.twig',[
            'status' => $status
        ]);
    }
}