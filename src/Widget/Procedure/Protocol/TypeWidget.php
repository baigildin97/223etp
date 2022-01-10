<?php
declare(strict_types=1);
namespace App\Widget\Procedure\Protocol;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('typeProtocol', [$this, 'type'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function type(Environment $twig, string $type): string {
        return $twig->render('widget/procedure/protocol/type.html.twig',[
            'type' => $type
        ]);
    }
}