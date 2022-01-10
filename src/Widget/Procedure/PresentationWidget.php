<?php
declare(strict_types=1);
namespace App\Widget\Procedure;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PresentationWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('presentation_type', [$this, 'presentationType'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function presentationType(Environment $twig, string $presentationForm): string {
        return $twig->render('widget/procedure/presentation.html.twig',[
            'presentation_form' => $presentationForm
        ]);
    }
}